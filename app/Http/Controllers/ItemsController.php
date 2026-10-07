<?php

namespace App\Http\Controllers;

use App\Models\Items;
use App\Models\DistributionItem;
use App\Models\ItemStatusHistory;
use App\Models\Locations;
use App\Http\Controllers\Concerns\ResolvesRedirects;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ItemsController extends Controller
{
    use ResolvesRedirects;
    /**
     * Display a listing of the resource.
     */
    public function searchItems(Request $request)
    {
        $q = $request->q;

        $items = Items::where('serial_number', 'like', "%$q%")
            ->orWhere('service_tag', 'like', "%$q%")
            ->orWhere('asset', 'like', "%$q%")
            ->orWhere('merk', 'like', "%$q%")
            ->orWhere('type', 'like', "%$q%")
            ->orWhere('processor', 'like', "%$q%")
            ->orWhere('os', 'like', "%$q%")
            ->orWhere('ram_gb', 'like', "%$q%")
            ->orWhere('tahun', 'like', "%$q%")
            ->limit(10)
            ->get();

        return response()->json(
            $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->serial_number . ' - ' . $item->merk . ' - ' . $item->type . ' - ' . ($item->asset ?? '-')
                ];
            })
        );
    }

    public function index(Request $request)
    {
        $activeDistribution = function ($di) {
            $di->where('status', 'dipakai')
                ->whereHas('distribution', function ($d) {
                    $d->where('status', 'dipakai');
                });
        };

        $query = Items::with([
            'barang_masuk',
            'storageLocation',
            'distributionItems' => function ($distributionItem) use ($activeDistribution) {
                $activeDistribution($distributionItem);
                $distributionItem->with('distribution.location')->latest();
            },
        ]);
        $selectedKategori = array_values(array_filter((array) $request->input('kategori', '')));
        $selectedMerk = array_values(array_filter((array) $request->input('merk', '')));
        $selectedAsset = array_values(array_filter((array) $request->input('asset', '')));
        $selectedSource = $request->input('source');

        if (! in_array($selectedSource, ['barang_masuk', 'master_item'], true)) {
            $selectedSource = null;
        }

         if ($request->item_id) {
            // kalau pilih dari suggestion → pakai ID saja
            $query->where('id', $request->item_id);
        }
            elseif ($request->filled('search')) {
                // kalau manual ketik → pakai search
                $search = '%' . $request->search . '%';
                $query->where(function ($q) use ($search) {
                $q->where('serial_number', 'like', $search)
                  ->orWhere('service_tag', 'like', $search)
                  ->orWhere('asset', 'like', $search)
                  ->orWhere('merk', 'like', $search)
                  ->orWhere('type', 'like', $search)
                  ->orWhere('processor', 'like', $search)
                  ->orWhere('os', 'like', $search)
                  ->orWhere('ram_gb', 'like', $search)
                  ->orWhere('tahun','like', $search)
                  ->orWhere('condition_note','like',$search);
                });
        }

        // Filter by kategori
        if (!empty($selectedKategori)) {
            $query->whereIn('kategori', $selectedKategori);
        }

        // Filter by merk
        if (!empty($selectedMerk)) {
            $query->whereIn('merk', $selectedMerk);
        }

        // Filter by asset/kepemilikan
        if (!empty($selectedAsset)) {
            $query->whereIn('asset', $selectedAsset);
        }

        if ($selectedSource === 'barang_masuk') {
            $query->whereNotNull('barang_masuk_id');
        } elseif ($selectedSource === 'master_item') {
            $query->whereNull('barang_masuk_id');
        }

       if ($request->status) {
            // USED khusus printer dari distribusi
            if ($request->status == 'used') {

                $query->where(function($q) use ($activeDistribution) {

                    // printer dipakai
                    $q->where(function($qq) use ($activeDistribution) {

                        $qq->whereIn('kategori', ['Printer Kertas', 'Printer Barcode'])
                            ->whereHas('distributionItems', $activeDistribution);

                    })

                    // non printer used biasa
                    ->orWhere(function($qq) {

                        $qq->whereNotIn('kategori', ['Printer Kertas', 'Printer Barcode'])
                        ->where('status', 'used');

                    });

                });

            } else {

                // Status non-used harus sesuai status yang tampil di tabel.
                // Printer yang masih punya distribusi aktif tetap dianggap "Digunakan".
                $query->where('status', $request->status)
                    ->where(function ($q) use ($activeDistribution) {
                        $q->whereNotIn('kategori', ['Printer Kertas', 'Printer Barcode'])
                            ->orWhere(function ($printerQuery) use ($activeDistribution) {
                                $printerQuery->whereIn('kategori', ['Printer Kertas', 'Printer Barcode'])
                                    ->whereDoesntHave('distributionItems', $activeDistribution);
                            });
                    });

            }

        }

        if ($request->kelengkapan === 'tanpa_detail') {
            $query->whereDoesntHave('device_detail');
        }

        $items = $query->latest()->paginate(50)->appends($request->query());
        $items->getCollection()->each(function (Items $item) {
            $item->refreshStatus();
        });

        // Get all kategori options
        $kategoriOptions = Items::getKategoriOptions();

        // Get distinct merk list
        $merkList = Items::select('merk')
            ->whereNotNull('merk')
            ->where('merk', '!=', '')
            ->distinct()
            ->orderBy('merk')
            ->pluck('merk')
            ->toArray();

        // Get distinct asset list
        $assetList = Items::select('asset')
            ->whereNotNull('asset')
            ->where('asset', '!=', '')
            ->distinct()
            ->orderBy('asset')
            ->pluck('asset')
            ->toArray();

        // Get current filters for display
        $filters = [
            'kategori' => $selectedKategori,
            'merk' => $selectedMerk,
            'asset' => $selectedAsset,
            'search' => $request->search,
            'source' => $selectedSource,
        ];

        return view('items.index', compact('items', 'kategoriOptions', 'merkList', 'assetList', 'filters'));
    }

    public function export(Request $request, string $format)
    {
        $selectedKategori = array_values(array_filter((array) $request->input('kategori', '')));
        $selectedMerk = array_values(array_filter((array) $request->input('merk', '')));
        $selectedAsset = array_values(array_filter((array) $request->input('asset', '')));
        $selectedSource = $request->input('source');

        if (! in_array($selectedSource, ['barang_masuk', 'master_item'], true)) {
            $selectedSource = null;
        }

        $items = $this->exportItemsQuery($request, $selectedKategori, $selectedMerk, $selectedAsset, $selectedSource)
            ->latest()
            ->get();

        $items->each(function (Items $item) {
            $item->refreshStatus();
        });

        $data = [
            'items' => $items,
            'filterLabel' => $this->itemsExportFilterLabel($request, $selectedKategori, $selectedMerk, $selectedAsset, $selectedSource),
        ];

        $filename = 'master-data-barang-' . now()->format('Ymd-His');

        if ($format === 'excel') {
            return response()->streamDownload(function () use ($data) {
                echo view('items.export_excel', $data)->render();
            }, $filename . '.xls', [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            ]);
        }

        if ($format === 'pdf') {
            return view('items.export_pdf', $data);
        }

        abort(404);
    }

    private function exportItemsQuery(Request $request, array $selectedKategori, array $selectedMerk, array $selectedAsset, ?string $selectedSource)
    {
        $activeDistribution = function ($di) {
            $di->where('status', 'dipakai')
                ->whereHas('distribution', function ($d) {
                    $d->where('status', 'dipakai');
                });
        };

        $query = Items::with([
            'barang_masuk',
            'storageLocation',
            'distributionItems' => function ($distributionItem) use ($activeDistribution) {
                $activeDistribution($distributionItem);
                $distributionItem->with('distribution.location')->latest();
            },
        ]);

        if ($request->item_id) {
            $query->where('id', $request->item_id);
        } elseif ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('serial_number', 'like', $search)
                    ->orWhere('service_tag', 'like', $search)
                    ->orWhere('asset', 'like', $search)
                    ->orWhere('merk', 'like', $search)
                    ->orWhere('type', 'like', $search)
                    ->orWhere('processor', 'like', $search)
                    ->orWhere('os', 'like', $search)
                    ->orWhere('ram_gb', 'like', $search)
                    ->orWhere('tahun', 'like', $search)
                    ->orWhere('condition_note', 'like', $search);
            });
        }

        if (! empty($selectedKategori)) {
            $query->whereIn('kategori', $selectedKategori);
        }

        if (! empty($selectedMerk)) {
            $query->whereIn('merk', $selectedMerk);
        }

        if (! empty($selectedAsset)) {
            $query->whereIn('asset', $selectedAsset);
        }

        if ($selectedSource === 'barang_masuk') {
            $query->whereNotNull('barang_masuk_id');
        } elseif ($selectedSource === 'master_item') {
            $query->whereNull('barang_masuk_id');
        }

        if ($request->status) {
            if ($request->status == 'used') {
                $query->where(function ($q) use ($activeDistribution) {
                    $q->where(function ($qq) use ($activeDistribution) {
                        $qq->whereIn('kategori', ['Printer Kertas', 'Printer Barcode'])
                            ->whereHas('distributionItems', $activeDistribution);
                    })->orWhere(function ($qq) {
                        $qq->whereNotIn('kategori', ['Printer Kertas', 'Printer Barcode'])
                            ->where('status', 'used');
                    });
                });
            } else {
                $query->where('status', $request->status)
                    ->where(function ($q) use ($activeDistribution) {
                        $q->whereNotIn('kategori', ['Printer Kertas', 'Printer Barcode'])
                            ->orWhere(function ($printerQuery) use ($activeDistribution) {
                                $printerQuery->whereIn('kategori', ['Printer Kertas', 'Printer Barcode'])
                                    ->whereDoesntHave('distributionItems', $activeDistribution);
                            });
                    });
            }
        }

        if ($request->kelengkapan === 'tanpa_detail') {
            $query->whereDoesntHave('device_detail');
        }

        return $query;
    }

    private function itemsExportFilterLabel(Request $request, array $selectedKategori, array $selectedMerk, array $selectedAsset, ?string $selectedSource): string
    {
        $filters = [];

        if ($request->filled('search')) {
            $filters[] = 'Cari: ' . $request->search;
        }

        if (! empty($selectedKategori)) {
            $filters[] = 'Kategori: ' . implode(', ', $selectedKategori);
        }

        if (! empty($selectedMerk)) {
            $filters[] = 'Merk: ' . implode(', ', $selectedMerk);
        }

        if (! empty($selectedAsset)) {
            $filters[] = 'Asset: ' . implode(', ', $selectedAsset);
        }

        if ($request->filled('status')) {
            $filters[] = 'Kondisi: ' . $this->itemStatusLabel($request->status);
        }

        if ($selectedSource) {
            $filters[] = 'Asal Data: ' . ($selectedSource === 'barang_masuk' ? 'Barang Masuk' : 'Master Item');
        }

        if ($request->filled('item_id')) {
            $filters[] = 'Pilihan suggestion';
        }

        return empty($filters) ? 'Semua data' : implode(' | ', $filters);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $kategoriOptions = Items::getKategoriOptions();
        $locations = $this->itemStorageLocations();
        $redirect = $this->redirectTarget($request, route('items.index'));

        return view('items.create', compact('kategoriOptions','locations', 'redirect'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|in:PC,Monitor,Printer Kertas,Printer Barcode,Scanner,Lainnya',
            'merk' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'asset' => 'nullable|string|max:255',
            'serial_number' => 'required|string|unique:items,serial_number',
            'service_tag' => 'nullable|string|max:255',
            'processor' => 'nullable|string|max:255',
            'ram_gb' => 'nullable|integer|min:1',
            'storage_gb' => 'nullable|integer|min:1',
            'vga' => 'nullable|string|max:255',
            'os' => 'nullable|string|max:255',
            'tahun' => 'nullable|digits:4',
            'storage_location_id' => 'nullable|exists:locations,id',

        ]);

        Items::create($validated);

        return redirect($this->redirectTarget($request, route('items.index')))->with('success', 'Data berhasil diperbarui!');
    }

    public function detail(Request $request,Items $item)
    {
        $item->refreshStatus();
        $item->refresh();

        $item->load([
        'storageLocation',
        'barang_masuk',
        ]);

        $locations = $this->itemStorageLocations();
        $redirect = $this->redirectTarget($request, route('items.index'));
        return view('items.detail', compact('item','locations','redirect'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Items $item, Request $request)
    {
            $item->refreshStatus();
            $item->refresh();

            $item->load([
                'device_detail',
                'storageLocation',
                'distributionItems.distribution.location'
            ]);

            $activeDistributions = $item->distributionItems()
                ->with(['distribution.location'])
                ->where('status', 'dipakai')
                ->whereHas('distribution', function ($distribution) {
                    $distribution->where('status', 'dipakai');
                })
                ->latest()
                ->get();

            $activeDistributionItem = $item->distributionItems()
            ->with([ 'distribution.location' ])
            ->where('status', 'dipakai')
            ->whereHas('distribution', function ($distribution) {
                $distribution->where('status', 'dipakai');
            })
            ->latest()
            ->first();

            $statusHistoryEvents = $this->statusHistoryEvents($item, $request);

            $locations = $this->itemStorageLocations();
            $statusHistoryFilters = $this->statusHistoryFilters($request);

            $redirect = $this->redirectTarget($request, route('items.index'));

            return view('items.show', compact(
            'item',
            'statusHistoryEvents',
            'activeDistributions',
            'activeDistributionItem',
            'redirect',
            'locations',
            'statusHistoryFilters'
            ));
    }

    public function exportHistory(Request $request, Items $item, string $format)
    {
        $statusHistoryEvents = $this->statusHistoryEvents($item, $request, false);
        $filename = 'riwayat-status-barang-' . $item->serial_number . '-' . now()->format('Ymd-His');

        if ($format === 'excel') {
            return $this->downloadHistoryExcel($item, $statusHistoryEvents, $filename . '.xls');
        }

        if ($format === 'pdf') {
            return view('items.history_pdf', [
                'item' => $item,
                'statusHistoryEvents' => $statusHistoryEvents,
                'statusHistoryFilters' => $this->statusHistoryFilters($request),
            ]);
        }

        abort(404);
    }

    private function statusHistoryEvents(Items $item, Request $request, bool $paginate = true)
    {
        $filters = $this->statusHistoryFilters($request);
        $events = $this->buildStatusHistoryEvents($item);

        if ($filters['history_date_from']) {
            $dateFrom = Carbon::parse($filters['history_date_from'])->startOfDay();
            $events = $events->filter(fn ($event) => $event['sort_at'] && $event['sort_at']->gte($dateFrom));
        }

        if ($filters['history_date_to']) {
            $dateTo = Carbon::parse($filters['history_date_to'])->endOfDay();
            $events = $events->filter(fn ($event) => $event['sort_at'] && $event['sort_at']->lte($dateTo));
        }

        if ($filters['history_event_type']) {
            $events = $events->filter(fn ($event) => $event['event_type'] === $filters['history_event_type']);
        }

        $events = $events
            ->sortByDesc(fn ($event) => $event['sort_at']?->timestamp ?? 0)
            ->values();

        if (! $paginate) {
            return $events;
        }

        $perPage = max(10, min((int) $filters['history_per_page'], 100));
        $page = LengthAwarePaginator::resolveCurrentPage('history_page');

        return (new LengthAwarePaginator(
            $events->forPage($page, $perPage)->values(),
            $events->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => 'history_page',
            ]
        ))->withQueryString();
    }

    private function buildStatusHistoryEvents(Items $item)
    {
        $events = collect();

        $item->distributionItems()
            ->with(['distribution.location', 'returnStorageLocation'])
            ->get()
            ->each(function (DistributionItem $distributionItem) use ($events) {
                $distribution = $distributionItem->distribution;
                $location = $distribution?->location;
                $distributionAt = $this->distributionHistoryTimestamp($distributionItem);

                $events->push([
                    'sort_at' => $distributionAt,
                    'display_at' => $distributionAt,
                    'event_type' => 'distribution',
                    'event_label' => 'Distribusi',
                    'old_status' => 'available',
                    'new_status' => 'used',
                    'old_status_label' => $this->itemStatusLabel('available'),
                    'new_status_label' => $this->itemStatusLabel('used'),
                    'new_status_class' => $this->itemStatusClass('used'),
                    'actor' => $distribution?->nama_user ?: '-',
                    'actor_label' => 'Pengguna',
                    'location' => $this->formatLocation($location),
                    'note' => $distribution?->keterangan ?: 'Barang didistribusikan',
                ]);

                if (! $distributionItem->returned_at) {
                    return;
                }

                $returnedStatus = $this->returnedItemStatus($distributionItem);

                $events->push([
                    'sort_at' => Carbon::parse($distributionItem->returned_at),
                    'display_at' => Carbon::parse($distributionItem->returned_at),
                    'event_type' => 'return',
                    'event_label' => 'Pengembalian',
                    'old_status' => 'used',
                    'new_status' => $returnedStatus,
                    'old_status_label' => $this->itemStatusLabel('used'),
                    'new_status_label' => $this->itemStatusLabel($returnedStatus),
                    'new_status_class' => $this->itemStatusClass($returnedStatus),
                    'actor' => $distribution?->nama_user ?: '-',
                    'actor_label' => 'Pengguna',
                    'location' => $this->formatLocation($distributionItem->returnStorageLocation ?? $location),
                    'note' => $distributionItem->return_note ?: 'Barang dikembalikan',
                ]);
            });

        $item->statusHistories()
            ->with(['user', 'oldLocation', 'newLocation'])
            ->get()
            ->each(function (ItemStatusHistory $history) use ($events) {
                $events->push([
                    'sort_at' => $history->created_at,
                    'display_at' => $history->created_at,
                    'event_type' => 'manual',
                    'event_label' => 'Edit Master Barang',
                    'old_status' => $history->old_status,
                    'new_status' => $history->new_status,
                    'old_status_label' => $this->itemStatusLabel($history->old_status),
                    'new_status_label' => $this->itemStatusLabel($history->new_status),
                    'new_status_class' => $this->itemStatusClass($history->new_status),
                    'actor' => $history->user?->name ?: 'System',
                    'actor_label' => 'Diubah oleh',
                    'location' => $this->formatLocation($history->newLocation),
                    'note' => $history->note ?: $history->new_condition_note ?: 'Status barang diperbarui dari master barang',
                ]);
            });

        return $events;
    }

    private function distributionHistoryTimestamp(DistributionItem $distributionItem): ?Carbon
    {
        $distribution = $distributionItem->distribution;
        $createdAt = $distributionItem->created_at
            ? Carbon::parse($distributionItem->created_at)
            : ($distribution?->created_at ? Carbon::parse($distribution->created_at) : null);

        if (! $distribution?->tanggal_distribusi) {
            return $createdAt;
        }

        $distributionDate = Carbon::parse($distribution->tanggal_distribusi);

        if (! $createdAt) {
            return $distributionDate;
        }

        return $distributionDate->setTime(
            $createdAt->hour,
            $createdAt->minute,
            $createdAt->second
        );
    }

    private function statusHistoryFilters(Request $request): array
    {
        $eventType = $request->input('history_event_type');

        if (! in_array($eventType, ['distribution', 'return', 'manual'], true)) {
            $eventType = null;
        }

        return [
            'history_date_from' => $request->input('history_date_from'),
            'history_date_to' => $request->input('history_date_to'),
            'history_event_type' => $eventType,
            'history_per_page' => (int) $request->input('history_per_page', 10),
        ];
    }

    private function returnedItemStatus(DistributionItem $distributionItem): string
    {
        $returnNote = strtolower($distributionItem->return_note ?? '');

        if (
            $distributionItem->return_condition_status === 'maintenance'
            || str_contains($returnNote, 'rusak')
            || str_contains($returnNote, 'maintenance')
        ) {
            return 'maintenance';
        }

        $returnedAt = $distributionItem->returned_at
            ? Carbon::parse($distributionItem->returned_at)
            : null;

        if ($returnedAt && $this->hasOtherActiveDistributionAt($distributionItem, $returnedAt)) {
            return 'used';
        }

        return 'available';
    }

    private function hasOtherActiveDistributionAt(DistributionItem $distributionItem, Carbon $checkedAt): bool
    {
        return $distributionItem->item
            ? $distributionItem->item->distributionItems()
                ->with('distribution')
                ->whereKeyNot($distributionItem->id)
                ->get()
                ->contains(function (DistributionItem $otherDistributionItem) use ($checkedAt) {
                    $startedAt = $this->distributionHistoryTimestamp($otherDistributionItem)
                        ?? ($otherDistributionItem->created_at ? Carbon::parse($otherDistributionItem->created_at) : null);

                    if (! $startedAt || $startedAt->gt($checkedAt)) {
                        return false;
                    }

                    if (! $otherDistributionItem->returned_at) {
                        return true;
                    }

                    return Carbon::parse($otherDistributionItem->returned_at)->gt($checkedAt);
                })
            : false;
    }

    private function itemStatusLabel(?string $status): string
    {
        return [
            'available' => 'Tersedia',
            'used' => 'Dipakai',
            'maintenance' => 'Pemeliharaan',
            'retired' => 'Tidak Digunakan',
            'vendor' => 'Dibawa Vendor',
        ][$status] ?? '-';
    }

    private function itemStatusClass(?string $status): string
    {
        return [
            'available' => 'bg-green-100 text-green-700',
            'used' => 'bg-blue-100 text-blue-700',
            'maintenance' => 'bg-red-100 text-red-700',
            'retired' => 'bg-gray-100 text-gray-700',
            'vendor' => 'bg-purple-100 text-purple-700',
        ][$status] ?? 'bg-gray-100 text-gray-700';
    }

    private function formatLocation(?Locations $location): string
    {
        if (! $location) {
            return '-';
        }

        return ($location->gedung ?? '-') . ' - ' . ($location->ruangan ?? '-');
    }

    private function historyDistributionQuery(Items $item, Request $request)
    {
        $query = $item->distributionItems()
            ->with('distribution.location')
            ->orderByDesc('created_at');

        $dateFrom = $request->input('history_date_from');
        $dateTo = $request->input('history_date_to');
        $dateType = $request->input('history_date_type', 'used');
        $returnStatus = $request->input('history_return_status');

        if ($dateFrom) {
            if ($dateType === 'returned') {
                $query->whereDate('returned_at', '>=', $dateFrom);
            } else {
                $query->whereHas('distribution', function ($q) use ($dateFrom) {
                    $q->whereDate('tanggal_distribusi', '>=', $dateFrom);
                });
            }
        }

        if ($dateTo) {
            if ($dateType === 'returned') {
                $query->whereDate('returned_at', '<=', $dateTo);
            } else {
                $query->whereHas('distribution', function ($q) use ($dateTo) {
                    $q->whereDate('tanggal_distribusi', '<=', $dateTo);
                });
            }
        }

        if ($returnStatus === 'dipakai') {
            $query->where('status', 'dipakai');
        }

        if ($returnStatus === 'dikembalikan') {
            $query->where('status', 'dikembalikan');
        }

        if ($returnStatus === 'normal') {
            $query->where('status', 'dikembalikan')
                ->where(function ($q) {
                    $q->where('return_condition_status', 'available')
                        ->orWhereNull('return_condition_status');
                })
                ->where(function ($q) {
                    $q->whereNull('return_note')
                        ->orWhere(function ($note) {
                            $note->where('return_note', 'not like', '%rusak%')
                                ->where('return_note', 'not like', '%maintenance%');
                        });
                });
        }

        if ($returnStatus === 'maintenance') {
            $query->where('status', 'dikembalikan')
                ->where(function ($q) {
                    $q->where('return_condition_status', 'maintenance')
                        ->orWhere('return_note', 'like', '%rusak%')
                        ->orWhere('return_note', 'like', '%maintenance%');
                });
        }

        return $query;
    }

    private function historyFilters(Request $request): array
    {
        return [
            'history_date_from' => $request->input('history_date_from'),
            'history_date_to' => $request->input('history_date_to'),
            'history_date_type' => $request->input('history_date_type', 'used'),
            'history_return_status' => $request->input('history_return_status'),
            'history_per_page' => (int) $request->input('history_per_page', 10),
        ];
    }

    private function downloadHistoryExcel(Items $item, $statusHistoryEvents, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($item, $statusHistoryEvents) {
            echo view('items.history_excel', [
                'item' => $item,
                'statusHistoryEvents' => $statusHistoryEvents,
            ])->render();
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Items $item)
    {
        $item->refreshStatus();
        $item->refresh();

        $kategoriOptions = Items::getKategoriOptions();
        $locations = $this->itemStorageLocations();
        $redirect = $this->redirectTarget($request, route('items.index'));
        return view('items.edit', compact('item', 'kategoriOptions', 'locations', 'redirect'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Items $item)
    {
        $validated = $request->validate([
            'kategori' => 'required|in:PC,Monitor,Printer Kertas,Printer Barcode,Scanner,Lainnya',
            'merk' =>  $item->barang_masuk_id ?'nullable':'required|string|max:255',
            'type' => 'required|string|max:255',
            'asset' => 'nullable|string|max:255',
            'serial_number' => 'required|string|unique:items,serial_number,' . $item->id,
            'service_tag' => 'nullable|string|max:255',
            'processor' => 'nullable|string|max:255',
            'ram_gb' => 'nullable|integer|min:1',
            'storage_gb' => 'nullable|integer|min:1',
            'vga' => 'nullable|string|max:255',
            'os' => 'nullable|string|max:255',
            'tahun' => 'nullable|digits:4',
            'storage_location_id' => 'nullable|exists:locations,id',
            'status' => 'nullable|in:used,available,maintenance,retired,vendor',
            'condition_note' => 'nullable|string|max:255',

        ]);

        $item->refreshStatus();
        $item->refresh();

        $beforeStatus = [
            'status' => $item->status,
            'storage_location_id' => $item->storage_location_id,
            'condition_note' => $item->condition_note,
        ];

        //TIDAK BOLEH UBAH STATUS JIKA USED
        if ($item->isUsed()){
            // paksa status tetap used
            $validated['status'] = 'used';
             // lokasi juga jangan berubah
            unset($validated['storage_location_id']);
        } elseif (($validated['status'] ?? null) === 'used') {
            $validated['status'] = 'available';
        }

        // HANDLE MERK DI SINI
        if ($item->barang_masuk_id) {
            $validated['merk'] = $item->merk; // paksa pakai yang lama
        }

        // reset note kalau available
        if (
            isset($validated['status']) &&
            $validated['status'] == 'available'
        ) {
            $validated['condition_note'] = null;
        }

        $item->update($validated);
        $item->refresh();
        $this->recordMasterStatusHistory($item, $beforeStatus);

        return redirect($this->redirectTarget($request, route('items.index')))->with('success', 'Data berhasil diperbarui!');
    }

    private function recordMasterStatusHistory(Items $item, array $beforeStatus): void
    {
        $statusChanged = ($beforeStatus['status'] ?? null) !== $item->status;
        $locationChanged = (string) ($beforeStatus['storage_location_id'] ?? '') !== (string) ($item->storage_location_id ?? '');
        $noteChanged = (string) ($beforeStatus['condition_note'] ?? '') !== (string) ($item->condition_note ?? '');

        if (! $statusChanged && ! $locationChanged && ! $noteChanged) {
            return;
        }

        ItemStatusHistory::create([
            'item_id' => $item->id,
            'user_id' => auth()->id(),
            'old_location_id' => $beforeStatus['storage_location_id'] ?? null,
            'new_location_id' => $item->storage_location_id,
            'source' => 'master_edit',
            'event' => 'status_changed',
            'old_status' => $beforeStatus['status'] ?? null,
            'new_status' => $item->status,
            'old_condition_note' => $beforeStatus['condition_note'] ?? null,
            'new_condition_note' => $item->condition_note,
            'note' => $this->masterStatusHistoryNote($item, $beforeStatus),
        ]);
    }

    private function masterStatusHistoryNote(Items $item, array $beforeStatus): string
    {
        $notes = [];

        if (($beforeStatus['status'] ?? null) !== $item->status) {
            $notes[] = 'Status diubah dari ' .
                $this->itemStatusLabel($beforeStatus['status'] ?? null) .
                ' ke ' .
                $this->itemStatusLabel($item->status);
        }

        if ((string) ($beforeStatus['storage_location_id'] ?? '') !== (string) ($item->storage_location_id ?? '')) {
            $notes[] = 'Lokasi penyimpanan diperbarui';
        }

        if ((string) ($beforeStatus['condition_note'] ?? '') !== (string) ($item->condition_note ?? '')) {
            $notes[] = 'Keterangan kondisi diperbarui';
        }

        return implode('. ', $notes) ?: 'Data status barang diperbarui';
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Int $id)
    {
        $item = Items::findOrfail($id);
        $item->refreshStatus();
        $item->refresh();

        // Cek apakah item sedang dipakai
        if ($item->isUsed()){
            return back()->with('error', 'Item sedang digunakan, tidak bisa dihapus !');
        }
        // Cek apakah pernah masuk distribusi
        $dipakai = DistributionItem::where('item_id',$id)->exists();

        if($dipakai){
            return back()->with('error', 'Item sudah pernah didistribusikan !');
        }
        //update quantity otomatis saat hapus
        $barangMasuk = $item->barang_masuk;
        $item->delete();

        if ($barangMasuk) {
            // Update quantity
            $barangMasuk->quantity = $barangMasuk->items()->count();
            $barangMasuk->save();
        }


        return redirect($this->redirectTarget($request, route('items.index')))->with('success', 'Item berhasil dihapus !');
    }

    private function itemStorageLocations()
    {
        return Locations::whereIn('type', ['warehouse', 'maintenance', 'vendor'])
            ->orderBy('type')
            ->orderBy('gedung')
            ->orderBy('ruangan')
            ->get();
    }
}
