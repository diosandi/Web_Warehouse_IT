<?php

namespace App\Http\Controllers;

use App\Models\DistributionItem;
use App\Models\MaintenanceBerkala;
use App\Models\Locations;
use Illuminate\Http\Request;

class MaintenanceBerkalaController extends Controller
{
    public function my(Request $request)
    {
        $periode = now()->format('Y-m');

        $distributionItems = DistributionItem::with([
                'item',
                'distribution.location',
                'maintenanceBerkalas' => fn ($query) => $query->latest('tanggal_cek'),
            ])
            ->active()
            ->whereHas('distribution', function ($distribution) use ($request) {
                $distribution->where('user_id', $request->user()->id);
            })
            ->whereHas('item', fn ($item) => $item->where('kategori', 'PC'))
            ->latest()
            ->get();

        return view('maintenance_berkala.my', compact('distributionItems', 'periode'));
    }

    public function search(Request $request)
    {
        $q = $request->q;

        $distributionItems = DistributionItem::with([
                'item',
                'distribution.location',
                'maintenanceBerkalas' => fn ($query) => $query->latest('tanggal_cek'),
            ])
            ->active()
            ->whereHas('item', fn ($item) => $item->where('kategori', 'PC'))
            ->where(function ($query) use ($q) {
                $query->whereHas('item', function ($item) use ($q) {
                    $item->where('serial_number', 'like', "%{$q}%")
                        ->orWhere('merk', 'like', "%{$q}%")
                        ->orWhere('asset', 'like', "%{$q}%");
                })->orWhereHas('distribution', function ($distribution) use ($q) {
                    $distribution->where('nama_user', 'like', "%{$q}%")
                        ->orWhere('divisi', 'like', "%{$q}%")
                        ->orWhereHas('user', function ($user) use ($q) {
                            $user->where('name', 'like', "%{$q}%");
                        });
                });
            })
            ->limit(10)
            ->get();

        return response()->json(
            $distributionItems->map(function ($distItem) {
                return [
                    'id' => $distItem->id,
                    'text' => "{$distItem->item->serial_number} - {$distItem->item->merk} - {$distItem->distribution->nama_user} - {$distItem->distribution->divisi}"
                ];
            })
        );
    }

    public function index(Request $request)
    {
        $periode = $request->get('periode', now()->format('Y-m'));
        $search = trim((string) $request->get('search', ''));
        $maintenanceBerkalaId = $request->get('maintenance_berkala_id');
        $query = MaintenanceBerkala::with([
            'item.storageLocation',
            'item.distributionItems.distribution.location',
        ]);

        $distributionItemsQuery = DistributionItem::with([
                'item',
                'distribution.location',
                'distribution.user',
                'maintenanceBerkalas' => fn ($query) => $query->latest('tanggal_cek'),
            ])
            ->active()
            ->whereHas('item', fn ($item) => $item->where('kategori', 'PC'));

        $filterStatusCek = $request->get('status_cek');
        if ($filterStatusCek === 'sudah_dicek'){
            $distributionItemsQuery->whereHas('maintenanceBerkalas', function ($query) use ($periode) {
                $query->where('periode_bulan', $periode);
            });
        }
        if ($filterStatusCek === 'belum_dicek') {
            $distributionItemsQuery->whereDoesntHave('maintenanceBerkalas', function ($query) use ($periode) {
                $query->where('periode_bulan', $periode);
            });
        }

        if ($maintenanceBerkalaId) {
            $distributionItemsQuery->where('id', $maintenanceBerkalaId);
        } elseif ($search !== '') {
            $distributionItemsQuery->where(function ($query) use ($search) {
                $query->whereHas('item', function ($item) use ($search) {
                    $item->where('serial_number', 'like', "%{$search}%")
                        ->orWhere('merk', 'like', "%{$search}%")
                        ->orWhere('asset', 'like', "%{$search}%");
                })->orWhereHas('distribution', function ($distribution) use ($search) {
                    $distribution->where('nama_user', 'like', "%{$search}%")
                        ->orWhere('divisi', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($user) use ($search) {
                            $user->where('name', 'like', "%{$search}%");
                        });
                });
            });
        }

        $query = MaintenanceBerkala::with([
            'item.storageLocation',
            'item.distributionItems.distribution.location',
        ]);

        $gedungs = Locations::where('type', 'distribution')
            ->select('gedung')
            ->whereNotNull('gedung')
            ->distinct()
            ->orderBy('gedung')
            ->pluck('gedung');

        $locationsByGedung = Locations::where('type', 'distribution')
            ->select('gedung', 'ruangan')
            ->whereNotNull('gedung')
            ->whereNotNull('ruangan')
            ->distinct()
            ->orderBy('gedung')
            ->orderBy('ruangan')
            ->get()
            ->groupBy('gedung')
            ->map(fn ($rooms) => $rooms->pluck('ruangan')->filter()->unique()->sort()->values()->all());

        $selectedGedung = $request->input('gedung');
        $selectedRuangan = $request->input('ruangan');

        if ($selectedGedung) {
            $distributionItemsQuery->whereHas('distribution.location', function ($location) use ($selectedGedung) {
                $location->where('gedung', $selectedGedung);
            });
        }

        if ($selectedRuangan) {
            $distributionItemsQuery->whereHas('distribution.location', function ($location) use ($selectedRuangan) {
                $location->where('ruangan', $selectedRuangan);
            });
        }


        $distributionItems = $this->maintenanceItemsQuery($request, $periode, $search)
            ->paginate(50)
            ->withQueryString();

        return view('maintenance_berkala.index', compact(
            'distributionItems',
            'periode',
            'search',
            'gedungs',
            'locationsByGedung',
        ));
    }

    public function export(Request $request, string $format)
    {
        $periode = $request->get('periode', now()->format('Y-m'));
        $search = trim((string) $request->get('search', ''));

        $distributionItems = $this->maintenanceItemsQuery($request,$periode, $search)
            ->get();

        $filename = 'maintenance_bulanan-' .$periode . '-' . now()->format('Ymd_His');

        if ($format === 'excel') {
             return response()->streamDownload(function () use ($distributionItems, $periode) {
            echo view('maintenance_berkala.maintenance_excel', compact(
                'distributionItems',
                'periode'
            ))->render();
        }, $filename . '.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    if ($format === 'pdf') {
        return view('maintenance_berkala.maintenance_pdf', compact(
            'distributionItems',
            'periode'
        ));
    }

    abort(404);
    }

    private function maintenanceItemsQuery(Request $request,string $periode, string $search)
    {
        $maintenanceBerkalaId = $request->get('maintenance_berkala_id');

        $query = DistributionItem::with([
                'item',
                'distribution.location',
                'distribution.user',
                'maintenanceBerkalas' => fn ($query) => $query->latest('tanggal_cek'),
            ])
            ->active()
            ->whereHas('item', fn ($item) => $item->where('kategori', 'PC'));

        if ($maintenanceBerkalaId) {
            $query->where('id', $maintenanceBerkalaId);
        } elseif ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->whereHas('item', function ($item) use ($search) {
                    $item->where('serial_number', 'like', "%{$search}%")
                        ->orWhere('merk', 'like', "%{$search}%")
                        ->orWhere('asset', 'like', "%{$search}%");
                })->orWhereHas('distribution', function ($distribution) use ($search) {
                    $distribution->where('nama_user', 'like', "%{$search}%")
                        ->orWhere('divisi', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($user) use ($search) {
                            $user->where('name', 'like', "%{$search}%");
                        });
                });
            });
        }

        if ($request->get('status_cek')==='sudah_dicek'){
            $query->whereHas('maintenanceBerkalas', function ($maintenance) use ($periode){
                $maintenance->where('periode_bulan', $periode);
            });
        }
        if ($request->get('status_cek')==='belum_dicek'){
            $query->whereDoesntHave('maintenanceBerkalas', function ($maintenance) use ($periode){
                $maintenance->where('periode_bulan', $periode);
            });
        }
        if ($request->get('gedung')) {
            $query->whereHas('distribution.location', function ($location) use ($request) {
                $location->where('gedung', $request->get('gedung'));
            });
        }
        if ($request->get('ruangan')) {
            $query->whereHas('distribution.location', function ($location) use ($request) {
                $location->where('ruangan', $request->get('ruangan'));
            });
        }
        return $query->latest();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'distribution_item_id' => 'required|exists:distribution_items,id',
            'tanggal_cek' => 'required|date',
            'periode_bulan' => 'required|date_format:Y-m',
            'kondisi' => 'required|in:baik,perlu_perbaikan',
            'checklist' => 'nullable|array',
            'checklist.*' => 'string|max:100',
            'catatan' => 'nullable|string|max:1000',
            'teknisi' => 'nullable|string|max:100',
        ]);

        $validated['status'] = $validated['kondisi'] === 'perlu_perbaikan'
            ? 'perlu_perbaikan'
            : 'sudah_dicek';

        $maintenance = MaintenanceBerkala::create($validated);

        if ($maintenance->status === 'perlu_perbaikan') {
            $maintenance->load('distributionItem.item');
            $maintenance->distributionItem->item?->update([
                'status' => 'maintenance',
                'condition_note' => $maintenance->catatan,
            ]);
        }

        return redirect()
            ->route('maintenance_berkala.index', ['periode' => $validated['periode_bulan']])
            ->with('success', 'Maintenance berkala berhasil dicatat.');
    }
}
