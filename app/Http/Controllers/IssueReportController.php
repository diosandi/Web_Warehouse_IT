<?php

namespace App\Http\Controllers;

use App\Models\DistributionItem;
use App\Models\IssueReport;
use App\Models\IssueReportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class IssueReportController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = $this->reportBaseQuery($request);
        $summary = $this->issueReportSummary($baseQuery);
        $reports = $this->filteredReportQuery($request, $baseQuery)
            ->latest()
            ->paginate(15)
            ->withQueryString();
        $categoryOptions = IssueReport::categoryOptions();
        $statusOptions = IssueReport::statusOptions();
        $priorityOptions = IssueReport::priorityOptions();

        return view('issue_reports.index', compact(
            'reports',
            'summary',
            'categoryOptions',
            'statusOptions',
            'priorityOptions'
        ));
    }

    public function create(Request $request)
    {
        $reportableItems = $this->reportableItemQuery($request)
            ->with(['item', 'distribution.location'])
            ->latest()
            ->get();

        $selectedDistributionItemId = old('distribution_item_id', $request->get('distribution_item_id'));
        $categoryOptions = IssueReport::categoryOptions();
        $priorityOptions = IssueReport::priorityOptions();

        return view('issue_reports.create', compact('reportableItems', 'selectedDistributionItemId', 'categoryOptions', 'priorityOptions'));
    }

    public function store(Request $request)
    {
        $selectedCategory = $request->input('issue_category', IssueReport::CATEGORY_DEVICE);

        $validated = $request->validate([
            'issue_category' => ['nullable', Rule::in(array_keys(IssueReport::categoryOptions()))],
            'distribution_item_id' => [
                Rule::requiredIf($selectedCategory === IssueReport::CATEGORY_DEVICE),
                'nullable',
                'integer',
                'exists:distribution_items,id',
            ],
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'priority' => ['required', Rule::in(array_keys(IssueReport::priorityOptions()))],
            'evidence' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4,webm,avi,mov,mkv', 'max:10240'],
        ]);

        $validated['issue_category'] = $validated['issue_category'] ?? IssueReport::CATEGORY_DEVICE;
        $distributionItem = null;

        if ($validated['issue_category'] === IssueReport::CATEGORY_DEVICE) {
            $distributionItem = $this->reportableItemQuery($request)
                ->with(['item', 'distribution.location'])
                ->whereKey($validated['distribution_item_id'])
                ->first();

            if (! $distributionItem) {
                return back()
                    ->withInput()
                    ->with('error', 'Perangkat ini tidak tersedia untuk akun kamu.');
            }
        }

        $distribution = $distributionItem?->distribution;
        $evidencePath = null;

        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('issue-reports', 'public');
        }

        $report = IssueReport::create([
            'reporter_id' => $request->user()->id,
            'distribution_id' => $distribution?->id,
            'distribution_item_id' => $distributionItem?->id,
            'item_id' => $distributionItem?->item_id,
            'location_id' => $distribution?->location_id,
            'issue_category' => $validated['issue_category'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'evidence_path' => $evidencePath,
            'priority' => $validated['priority'],
            'status' => IssueReport::STATUS_OPEN,
        ]);

        return redirect()
            ->route('issue_reports.show', $report)
            ->with('success', 'Laporan kendala berhasil dikirim.');
    }

    public function show(Request $request, IssueReport $issueReport)
    {
        $this->authorizeReportAccess($request, $issueReport);

        $issueReport->load([
            'reporter',
            'resolver',
            'item',
            'location',
            'distribution.location',
            'distributionItem.item',
            'messages.sender',
        ]);

        $statusOptions = IssueReport::statusOptions();
        $priorityOptions = IssueReport::priorityOptions();
        $initialMessages = $issueReport->messages->map(fn ($message) => $this->formatMessage($message))->values();
        $canSendMessages = $this->canSendMessages($issueReport);
        $messageLockReason = $this->messageLockReason($issueReport);

        return view('issue_reports.show', compact('issueReport', 'statusOptions', 'priorityOptions', 'initialMessages', 'canSendMessages', 'messageLockReason'));
    }

    public function updateStatus(Request $request, IssueReport $issueReport)
    {
        abort_unless($request->user()->canManageIssueReports(), 403);

        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(IssueReport::statusOptions()))],
            'priority' => ['required', Rule::in(array_keys(IssueReport::priorityOptions()))],
            'admin_note' => 'nullable|string|max:2000',
        ]);

        $updates = [
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'admin_note' => $validated['admin_note'] ?? null,
        ];

        if (in_array($validated['status'], [IssueReport::STATUS_RESOLVED, IssueReport::STATUS_CLOSED], true)) {
            $updates['resolved_at'] = $issueReport->resolved_at ?? now();
            $updates['resolved_by'] = $issueReport->resolved_by ?? $request->user()->id;
        } else {
            $updates['resolved_at'] = null;
            $updates['resolved_by'] = null;
        }

        $issueReport->update($updates);

        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function storeMessage(Request $request, IssueReport $issueReport)
    {
        $this->authorizeReportAccess($request, $issueReport);

        if (! $this->canSendMessages($issueReport)) {
            $message = $this->messageLockReason($issueReport);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 403);
            }

            return back()->withErrors($message);
        }

        $payload = $request->all();
        if (! isset($payload['message']) && $request->isJson()) {
            $payload = $request->json()->all();
        }

        $validated = validator($payload, [
            'message' => 'required|string|max:2000',
        ])->validate();

        $message = $issueReport->messages()->create([
            'sender_id' => $request->user()->id,
            'message' => trim($validated['message']),
        ]);

        $message->load('sender');

        if (! $request->expectsJson()) {
            return back()->with('success', 'Pesan berhasil dikirim.');
        }

        return response()->json($this->formatMessage($message), 201);
    }

    public function fetchMessages(Request $request, IssueReport $issueReport)
    {
        $this->authorizeReportAccess($request, $issueReport);

        $messages = $issueReport->messages()->with('sender')->get();

        return response()->json($messages->map(fn ($message) => $this->formatMessage($message))->values());
    }

    public function realtime(Request $request)
    {
        abort_unless($request->user()->canManageIssueReports(), 403);

        $lastSeenId = max((int) $request->query('last_seen_id', 0), 0);
        $latestReport = IssueReport::query()->latest('id')->first();
        $newReports = collect();

        if ($lastSeenId > 0) {
            $newReports = IssueReport::with($this->reportRelations())
                ->where('id', '>', $lastSeenId)
                ->latest('id')
                ->take(5)
                ->get();
        }

        $baseQuery = $this->reportBaseQuery($request);
        $payload = [
            'latest_id' => $latestReport?->id ?? 0,
            'new_count' => $newReports->count(),
            'reports' => $newReports
                ->map(fn (IssueReport $report) => $this->formatReportNotification($report))
                ->values(),
            'summary' => $this->issueReportSummary($baseQuery),
        ];

        if ($request->boolean('include_table')) {
            $reports = $this->filteredReportQuery($request, $baseQuery)
                ->latest()
                ->paginate(15)
                ->withQueryString();

            $payload['table'] = [
                'rows_html' => view('issue_reports.partials.report_rows', [
                    'reports' => $reports,
                    'isManager' => true,
                    'statusClasses' => $this->statusClasses(),
                    'priorityClasses' => $this->priorityClasses(),
                ])->render(),
                'pagination_html' => $reports->links()->toHtml(),
                'total' => $reports->total(),
                'current_page' => $reports->currentPage(),
                'last_page' => $reports->lastPage(),
            ];
        }

        return response()->json($payload);
    }

    private function formatMessage(IssueReportMessage $message): array
    {
        return [
            'id' => $message->id,
            'message' => $message->message,
            'sender' => $message->sender ? [
                'id' => $message->sender->id,
                'name' => $message->sender->name,
            ] : null,
            'created_at' => $message->created_at->format('d/m/Y H:i'),
        ];
    }

    private function canSendMessages(IssueReport $issueReport): bool
    {
        return $issueReport->status === IssueReport::STATUS_IN_PROGRESS;
    }

    private function messageLockReason(IssueReport $issueReport): string
    {
        return match ($issueReport->status) {
            IssueReport::STATUS_OPEN => 'Chat akan dibuka saat status laporan Diproses.',
            IssueReport::STATUS_RESOLVED, IssueReport::STATUS_CLOSED => 'Chat sudah ditutup karena laporan sudah selesai atau ditutup.',
            default => 'Chat belum tersedia untuk status laporan ini.',
        };
    }

    private function reportBaseQuery(Request $request)
    {
        $query = IssueReport::query();

        if (! $request->user()->canManageIssueReports()) {
            $query->where('reporter_id', $request->user()->id);
        }

        return $query;
    }

    private function issueReportSummary($baseQuery): array
    {
        return [
            'open' => (clone $baseQuery)->where('status', IssueReport::STATUS_OPEN)->count(),
            'in_progress' => (clone $baseQuery)->where('status', IssueReport::STATUS_IN_PROGRESS)->count(),
            'resolved' => (clone $baseQuery)->where('status', IssueReport::STATUS_RESOLVED)->count(),
            'closed' => (clone $baseQuery)->where('status', IssueReport::STATUS_CLOSED)->count(),
        ];
    }

    private function filteredReportQuery(Request $request, $baseQuery)
    {
        $query = (clone $baseQuery)->with($this->reportRelations());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if (
            $request->filled('issue_category') &&
            array_key_exists($request->issue_category, IssueReport::categoryOptions())
        ) {
            $query->where('issue_category', $request->issue_category);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';

            $query->where(function ($report) use ($search) {
                $report->where('ticket_number', 'like', $search)
                    ->orWhere('title', 'like', $search)
                    ->orWhere('description', 'like', $search)
                    ->orWhereHas('reporter', function ($user) use ($search) {
                        $user->where('name', 'like', $search)
                            ->orWhere('username', 'like', $search);
                    })
                    ->orWhereHas('item', function ($item) use ($search) {
                        $item->where('serial_number', 'like', $search)
                            ->orWhere('merk', 'like', $search)
                            ->orWhere('type', 'like', $search);
                    })
                    ->orWhereHas('location', function ($location) use ($search) {
                        $location->where('gedung', 'like', $search)
                            ->orWhere('ruangan', 'like', $search);
                    });
            });
        }

        return $query;
    }

    private function reportRelations(): array
    {
        return [
            'reporter',
            'resolver',
            'item',
            'location',
            'distribution.location',
        ];
    }

    private function formatReportNotification(IssueReport $report): array
    {
        return [
            'id' => $report->id,
            'ticket_number' => $report->ticket_number,
            'title' => $report->title,
            'reporter' => $report->reporter?->name ?? '-',
            'category' => $report->issue_category_label,
            'priority' => $report->priority_label,
            'status' => $report->status_label,
            'created_at' => $report->created_at->format('d/m/Y H:i'),
            'url' => route('issue_reports.show', $report),
        ];
    }

    private function statusClasses(): array
    {
        return [
            'open' => 'bg-yellow-100 text-yellow-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'resolved' => 'bg-green-100 text-green-800',
            'closed' => 'bg-gray-100 text-gray-800',
        ];
    }

    private function priorityClasses(): array
    {
        return [
            'low' => 'bg-gray-100 text-gray-800',
            'normal' => 'bg-blue-100 text-blue-800',
            'high' => 'bg-orange-100 text-orange-800',
            'urgent' => 'bg-red-100 text-red-800',
        ];
    }

    private function reportableItemQuery(Request $request)
    {
        $query = DistributionItem::query()
            ->where('status', 'dipakai')
            ->whereHas('distribution', function ($distribution) {
                $distribution->where('status', 'dipakai');
            });

        if (! $request->user()->canManageIssueReports()) {
            $query->whereHas('distribution', function ($distribution) use ($request) {
                $distribution->where('user_id', $request->user()->id);
            });
        }

        return $query;
    }

    private function authorizeReportAccess(Request $request, IssueReport $issueReport): void
    {
        if ($request->user()->canManageIssueReports()) {
            return;
        }

        abort_unless($issueReport->reporter_id === $request->user()->id, 403);
    }
}
