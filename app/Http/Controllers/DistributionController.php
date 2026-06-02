<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use App\Models\Items;
use App\Models\DistributionItem;
use App\Models\Locations;
use App\Http\Controllers\Concerns\ResolvesRedirects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistributionController extends Controller
{
    use ResolvesRedirects;
    //Search item per produk
    public function search(Request $request)
    {
        $query = $request->q;
        $kategori = $request->kategori;

       $items = Items::where('kategori', $kategori)
        ->where(function($q) use ($kategori) {

            // Printer boleh dipakai berkali-kali
            if ($kategori == 'Printer Kertas' || $kategori == 'Printer Barcode') {
                $q->whereIn('status', ['available', 'used']);
            } else {
                // selain printer harus available
                $q->where('status', 'available');
            }

        })
        ->where(function($q) use ($query) {
            $q->where('serial_number', 'like', "%$query%")
            ->orWhere('merk', 'like', "%$query%");
        })
        ->limit(10)
        ->get();

        return response()->json($items);
    }
    
    //Search index
    public function searchDistribution(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        $search = "%{$q}%";
        $results = Distribution::with(['distributionItems.item','location'])
            ->where('nama_user', 'like', $search)
            ->orWhere('divisi', 'like', $search)
            ->orWhereHas('location', function ($itemQuery) use ($search) {
                $itemQuery->where('gedung', 'like', $search)
                          ->orWhere('ruangan', 'like', $search);
            })
            ->orWhereHas('distributionItems.item', function ($itemQuery)use($search) {
                $itemQuery->where('serial_number','like',$search)
                          ->orWhere('merk','like',$search);
            })
            ->limit(10)
            ->get();

        $keyword = strtolower($q);
        $suggestions = $results->map(function($d) use ($keyword) {
        $matchedDistributionItem = $d->distributionItems->first(function ($distributionItem) use ($keyword) {
            $item = $distributionItem->item;

            if (!$item) {
                return false;
            }

            return str_contains(strtolower((string) $item->serial_number), $keyword)
                || str_contains(strtolower((string) $item->merk), $keyword)
                || str_contains(strtolower((string) $item->kategori), $keyword);
        });

        $item = $matchedDistributionItem?->item ?? $d->distributionItems->first()?->item;
        $location = $d->location;

        $itemText = $item
            ? ($item->serial_number . ' - ' . $item->merk)
            : '-';

        $locationText = $location
            ? ($location->gedung . ' - ' . $location->ruangan)
            : '-';


            return [
                    'id' => $item ? $item->id : null,
                    'merk' => $item ? $item->merk : '-',
                    'serial_number' => $item ? $item->serial_number : '-',
                    'gedung'=> $location ? $location->gedung : '-',
                    'ruangan'=>$location ? $location->ruangan : '-',
                    'nama_user' => $d->nama_user,
                    'text' => trim(
                        $itemText .
                    ' | Lokasi: '. $locationText .
                    ' | Nama User: ' . ($d->nama_user ?? '-')
                )
            ];
        });

        return response()->json($suggestions);
    }
    //Search dapat ruangan
    public function getRuangan(Request $request)
    {
        $ruangan = Locations::where('type', 'distribution')
            ->when($request->filled('gedung'), function ($query) use ($request) {
                $query->where('gedung', $request->gedung);
            })
            ->select('id', 'ruangan')
            ->orderBy('ruangan')
            ->get();

        return response()->json($ruangan);
    }

    public function index(Request $request)
    {
        $query = Distribution::with(['distributionItems.item', 'location']);

        // SEARCH
        if ($request->item_id) {
            $query->whereHas('distributionItems', function($q) use ($request) {
                $q->where('item_id', $request->item_id);
            });
        } elseif ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama_user', 'like', "%{$request->search}%")
                ->orWhere('divisi', 'like', "%{$request->search}%")
                ->orWhereHas('location', function($loc) use ($request) {
                    $loc->where('gedung', 'like', "%{$request->search}%")
                        ->orWhere('ruangan', 'like', "%{$request->search}%");
                })
                ->orWhereHas('distributionItems.item', function($item) use ($request) {
                    $item->where('serial_number', 'like', "%{$request->search}%")
                        ->orWhere('merk', 'like', "%{$request->search}%");
                });
            });
        }

        // FILTER STATUS
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // filter tanggal range
        if ($request->tanggal_dari && $request->tanggal_sampai) {
            $query->whereBetween('tanggal_distribusi', [
                $request->tanggal_dari,
                $request->tanggal_sampai
            ]);
        }

        // kalau cuma dari aja
        if ($request->tanggal_dari && !$request->tanggal_sampai) {
            $query->whereDate('tanggal_distribusi', '>=', $request->tanggal_dari);
        }

        // kalau cuma sampai aja
        if (!$request->tanggal_dari && $request->tanggal_sampai) {
            $query->whereDate('tanggal_distribusi', '<=', $request->tanggal_sampai);
        }

        // FILTER LOKASI
        if ($request->gedung) {
            $query->whereHas('location', function($q) use ($request) {
                $q->where('gedung', $request->gedung);
            });
        }

        if ($request->ruangan) {
            $query->where('location_id', $request->ruangan);
        }

        $distribution = $query->latest()->paginate(10)->withQueryString();

        $locations = Locations::where('type', 'distribution')->get();
        $warehouseLocations = Locations::where('type', 'warehouse')->get();

        return view('distribution.index', compact('distribution', 'locations','warehouseLocations'));
    }

    public function create(Request $request)
    {
        Locations::where('type', 'distribution')->get();
        $redirect = $this->redirectTarget($request, route('distribution.index'));

        // Get distinct gedung list
        $gedungList = Locations::where('type', 'distribution')
            ->distinct()
            ->pluck('gedung');

        return view('distribution.create', [
            'pcs' => Items::where('kategori','PC')
                ->where('status','available')
                ->whereDoesntHave('distributionItems', function($q){
                        $q->where('status', 'dipakai');
                    })
                    ->get(),
            'monitors' => Items::where('kategori','Monitor')
                ->where('status','available')
                ->whereDoesntHave('distributionItems', function($q){
                        $q->where('status', 'dipakai');
                    })
                    ->get(),
            'printers_kertas' => Items::where('kategori','Printer Kertas')
                ->where('status','available')
                ->whereDoesntHave('distributionItems', function($q){
                        $q->where('status', 'dipakai');
                    })
                    ->get(),
            'printers_barcode' => Items::where('kategori','Printer Barcode')
                ->where('status','available')
                ->whereDoesntHave('distributionItems', function($q){
                        $q->where('status', 'dipakai');
                    })
                    ->get(),
            'lainnya' => Items::where('kategori','Lainnya')
                ->where('status','available')
                ->whereDoesntHave('distributionItems', function($q){
                        $q->where('status', 'dipakai');
                    })
                    ->get(),
            'scanners' => Items::where('kategori','Scanner')
                ->where('status','available')
                ->whereDoesntHave('distributionItems', function($q){
                        $q->where('status', 'dipakai');
                    })
                    ->get(),
            'locations' => Locations::all(),
            'gedungList' => $gedungList,
            'redirect' => $redirect
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'location_id' => 'required|integer|exists:locations,id',
            'nama_user' => 'nullable',
            'divisi' => 'nullable',
            'tanggal_distribusi' => 'required|date',
            'items' => 'nullable|array',
            'items.*' => 'nullable|exists:items,id',
            'printer_kertas_ids' => 'nullable|array',
            'printer_kertas_ids.*' => 'exists:items,id',
            'printer_barcode_ids' => 'nullable|array',
            'printer_barcode_ids.*' => 'exists:items,id',
            'lainnya_ids' => 'nullable|array',
            'lainnya_ids.*' => 'exists:items,id',
        ]);

        $items = collect($request->input('items', []))
            ->merge($request->input('printer_kertas_ids', []))
            ->merge($request->input('printer_barcode_ids', []))
            ->merge($request->input('lainnya_ids',[]))
            ->filter()
            ->unique()
            ->values();

        // CEK MINIMAL 1 DEVICE
        if ($items->isEmpty()) {
            return back()->with(['error' => 'Minimal pilih 1 device!'])->withInput();
        }

        // CEK KHUSUS DEVICE EXCLUSIVE
        foreach ($items as $itemId) {
            $item = Items::find($itemId);
            $item->refreshStatus();

            if (in_array($item->kategori, ['PC', 'Monitor', 'Scanner', 'Lainnya'])) {

                $dipakai = DistributionItem::where('item_id', $itemId)
                    ->whereHas('distribution', function($q) {
                        $q->where('status', 'dipakai');
                    })
                    ->exists();

                if($dipakai){
                    $item = Items::find($itemId);
                    $item->update([
                        'status' => 'used',
                        'storage_location_id' => null
                    ]);
                }

                if ($dipakai) {
                    return back()->with('error', $item->serial_number . ' sudah dipakai!');
                }
            }
        }

        // 1. simpan header
        $distribution = Distribution::create([
            'location_id' => $request->location_id,
            'nama_user' => $request->nama_user,
            'divisi' => $request->divisi,
            'tanggal_distribusi' => $request->tanggal_distribusi,
            'status' => 'dipakai',
            'keterangan' => $request->keterangan,
        ]);

        // 2. simpan semua device
        foreach ($items as $itemId) {
            DistributionItem::create([
                'distribution_id' => $distribution->id,
                'item_id' => $itemId
            ]);

            $item = Items::find($itemId);
            $item->refreshStatus();

            // kalau bukan printer → set used
            if (!in_array($item->kategori, ['Printer Kertas', 'Printer Barcode'])) {
                $item->update(['status' => 'used']);
            }
        }
        return redirect($this->redirectTarget($request, route('distribution.index')))
            ->with('success', 'Distribusi berhasil disimpan');
    }

    public function edit(Request $request, $id)
    {
        $distribution = Distribution::with('distributionItems.item')->findOrFail($id);
        $redirect = $this->redirectTarget($request, route('distribution.index'));

        $activeDistributionItems = $distribution->distributionItems->where('status', 'dipakai');
        $selectedItems = $activeDistributionItems->pluck('item_id')->toArray();

        $pc_selected = $activeDistributionItems
        ->where('item.kategori', 'PC')
        ->first()?->item;

        $monitor_selected = $activeDistributionItems
        ->where('item.kategori', 'Monitor')
        ->first()?->item;

        $printer_kertas_selected = $activeDistributionItems
        ->filter(fn ($distributionItem) => optional($distributionItem->item)->kategori === 'Printer Kertas')
        ->pluck('item');

        $printer_barcode_selected = $activeDistributionItems
        ->filter(fn ($distributionItem) => optional($distributionItem->item)->kategori === 'Printer Barcode')
        ->pluck('item');

        $scanner_selected = $activeDistributionItems
        ->where('item.kategori', 'Scanner')
        ->first()?->item;

        $lainnya_selected = $activeDistributionItems
        ->filter(fn ($distributionItem) => optional($distributionItem->item)->kategori === 'Lainnya')
        ->pluck('item');

        $pcs = Items::where('kategori','PC')
            ->where(function($q) use ($selectedItems){

                $q->where(function($sub){

                    $sub->where('status','available')
                        ->whereDoesntHave('distributionItems', function($qq){
                            $qq->where('status','dipakai');
                        });

                })

                ->orWhereIn('id', $selectedItems);

            })->get();

        $monitors= Items::where('kategori','Monitor')
            ->where(function($q) use ($selectedItems){

                $q->where(function($sub){

                    $sub->where('status','available')
                        ->whereDoesntHave('distributionItems', function($qq){
                            $qq->where('status','dipakai');
                        });

                })

                ->orWhereIn('id', $selectedItems);

            })->get();

        $printers_kertas = Items::where('kategori','Printer Kertas')
            ->where(function($q) use ($selectedItems){

                $q->where(function($sub){

                    $sub->where('status','available')
                        ->whereDoesntHave('distributionItems', function($qq){
                            $qq->where('status','dipakai');
                        });

                })

                ->orWhereIn('id', $selectedItems);

            })->get();

        $printers_barcode = Items::where('kategori','Printer Barcode')
            ->where(function($q) use ($selectedItems){

                $q->where(function($sub){

                    $sub->where('status','available')
                        ->whereDoesntHave('distributionItems', function($qq){
                            $qq->where('status','dipakai');
                        });

                })

                ->orWhereIn('id', $selectedItems);

            })->get();

        $scanners = Items::where('kategori','Scanner')
            ->where(function($q) use ($selectedItems){

                $q->where(function($sub){

                    $sub->where('status','available')
                        ->whereDoesntHave('distributionItems', function($qq){
                            $qq->where('status','dipakai');
                        });

                })

                ->orWhereIn('id', $selectedItems);

            })->get();

        $lainnya = Items::where('kategori','Lainnya')
            ->where(function($q) use ($selectedItems){

                $q->where(function($sub){

                    $sub->where('status','available')
                        ->whereDoesntHave('distributionItems', function($qq){
                            $qq->where('status','dipakai');
                        });

                })

                ->orWhereIn('id', $selectedItems);

            })->get();

        Locations::where('type', 'distribution')->get();

        // Get distinct gedung list
        $gedungList = Locations::where('type', 'distribution')
            ->distinct()
            ->pluck('gedung');

        $locations = Locations::all();

        return view('distribution.edit', compact(
            'distribution',
            'selectedItems',
            'pcs','monitors','printers_kertas','printers_barcode','scanners','lainnya',
            'locations',
            'gedungList',
            'pc_selected',
            'monitor_selected',
            'printer_kertas_selected',
            'printer_barcode_selected',
            'scanner_selected',
            'lainnya_selected',
            'redirect'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'location_id' => 'required|integer|exists:locations,id',
            'tanggal_distribusi' => 'required|date',

            // TAMBAHAN
            'items' => 'nullable|array',
            'items.*' => 'nullable|exists:items,id',
            'printer_kertas_ids' => 'nullable|array',
            'printer_kertas_ids.*' => 'exists:items,id',
            'printer_barcode_ids' => 'nullable|array',
            'printer_barcode_ids.*' => 'exists:items,id',
            'lainnya_ids' => 'nullable|array',
            'lainnya_ids.*' => 'exists:items,id'
        ]);
        $distribution = Distribution::with('distributionItems.item')->findOrFail($id);

        $items = collect($request->input('items', []))
            ->merge($request->input('printer_kertas_ids', []))
            ->merge($request->input('printer_barcode_ids', []))
            ->merge($request->input('lainnya_ids',[]))
            ->filter()
            ->unique()
            ->values();

        if ($items->isEmpty()) {
            return back()->with(['error' => 'Minimal pilih 1 device!'])->withInput();
        }

        $activeDistributionItems = $distribution->distributionItems
            ->where('status', 'dipakai')
            ->keyBy('item_id');

        // CEK KHUSUS DEVICE EXCLUSIVE, kecuali device yang memang sedang aktif di distribusi ini
        foreach ($items as $itemId) {
            $item = Items::find($itemId);
            $alreadyActiveHere = $activeDistributionItems->has($itemId);
            $isPrinter = in_array($item->kategori, ['Printer Kertas', 'Printer Barcode']);

            if (
                !$alreadyActiveHere
                && (($isPrinter && !in_array($item->status, ['available', 'used'])) || (!$isPrinter && $item->status !== 'available'))
            ) {
                return back()->with('error', $item->serial_number . ' tidak tersedia untuk distribusi!')->withInput();
            }

            if (in_array($item->kategori, ['PC', 'Monitor', 'Scanner', 'Lainnya'])) {
                $dipakai = DistributionItem::where('item_id', $itemId)
                    ->where('distribution_id', '!=', $distribution->id)
                    ->where('status', 'dipakai')
                    ->whereHas('distribution', function($q) {
                        $q->where('status', 'dipakai');
                    })
                    ->exists();

                if ($dipakai) {
                    return back()->with('error', $item->serial_number . ' sudah dipakai!')->withInput();
                }
            }
        }

        $status = $items->isNotEmpty() ? 'dipakai' : 'dikembalikan';

        DB::transaction(function () use ($request, $distribution, $items, $activeDistributionItems, $status) {
            $distribution->update([
                'location_id' => $request->location_id,
                'nama_user' => $request->nama_user,
                'divisi' => $request->divisi,
                'tanggal_distribusi' => $request->tanggal_distribusi,
                'keterangan' => $request->keterangan,
                'status' => $status,
            ]);

            $newItemIds = $items->all();
            $removedDistributionItems = $activeDistributionItems
                ->reject(fn ($distributionItem, $itemId) => in_array($itemId, $newItemIds));

            foreach ($removedDistributionItems as $old) {
                $old->update([
                    'status' => 'dikembalikan',
                    'returned_at' => now(),
                    'return_condition_status' => $old->return_condition_status ?? 'available',
                    'return_note' => $old->return_note ?? 'Diganti melalui edit distribusi',
                ]);

                $old->item?->refreshStatus();
            }

            foreach ($items as $itemId) {
                if (!$activeDistributionItems->has($itemId)) {
                    DistributionItem::create([
                        'distribution_id' => $distribution->id,
                        'item_id' => $itemId,
                        'status' => 'dipakai',
                    ]);
                }

                $item = Items::find($itemId);

                if (!in_array($item->kategori, ['Printer Kertas', 'Printer Barcode'])) {
                    $item->update([
                        'status' => 'used',
                        'storage_location_id' => null,
                    ]);
                }
            }
        });

        return redirect($this->redirectTarget($request, route('distribution.index')))
            ->with('success', 'Distribusi berhasil diupdate');
    }

    public function returnItem(Request $request, $id)
    {
        $request->validate([
            'storage_location_id' => 'required|exists:locations,id',
            'condition_status' => 'required|in:available,maintenance',
            'condition_note' => 'nullable|string|max:255'
        ]);

        if ($request->isMethod('post')) {
            $distribution = Distribution::with('distributionItems.item')->findOrFail($id);
            $activeItems = $distribution->distributionItems->where('status', 'dipakai');

            if ($activeItems->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'Tidak ada barang aktif yang bisa dikembalikan');
            }

            DB::transaction(function () use ($request, $distribution, $activeItems) {
                foreach ($activeItems as $distributionItem) {
                    $this->returnDistributionItem(
                        $distributionItem,
                        $request->condition_status,
                        $request->storage_location_id,
                        $request->condition_note
                    );
                }

                $this->closeDistributionIfNoActiveItems($distribution);
            });

            return redirect()->back()
                ->with('success', 'Barang berhasil dikembalikan');
        }

        // ambil distribution item
        $distributionItem = DistributionItem::with([
            'item',
            'distribution'
        ])->findOrFail($id);

        DB::transaction(function () use ($request, $distributionItem) {
            $this->returnDistributionItem(
                $distributionItem,
                $request->condition_status,
                $request->storage_location_id,
                $request->condition_note
            );
        });

        return redirect()->back()
            ->with('success', 'Barang berhasil dikembalikan');
    }

    private function returnDistributionItem(
        DistributionItem $distributionItem,
        string $conditionStatus,
        int $storageLocationId,
        ?string $conditionNote
    ): void {
        $distributionItem->loadMissing(['item', 'distribution']);

        if (!$distributionItem->item || $distributionItem->status !== 'dipakai') {
            return;
        }

        if ($conditionStatus === 'maintenance' && $this->isSharedPrinter($distributionItem->item)) {
            $activePrinterItems = DistributionItem::with(['item', 'distribution'])
                ->where('item_id', $distributionItem->item_id)
                ->where('status', 'dipakai')
                ->get();

            foreach ($activePrinterItems as $activePrinterItem) {
                $this->markDistributionItemReturned(
                    $activePrinterItem,
                    $conditionStatus,
                    $conditionNote
                );

                $this->closeDistributionIfNoActiveItems($activePrinterItem->distribution);
            }

            $distributionItem->item->update([
                'status' => 'maintenance',
                'storage_location_id' => $storageLocationId,
                'condition_note' => $conditionNote,
            ]);

            return;
        }

        $this->markDistributionItemReturned(
            $distributionItem,
            $conditionStatus,
            $conditionNote
        );

        $this->syncReturnedItemStatus(
            $distributionItem->item,
            $conditionStatus,
            $storageLocationId,
            $conditionNote
        );

        $this->closeDistributionIfNoActiveItems($distributionItem->distribution);
    }

    private function markDistributionItemReturned(
        DistributionItem $distributionItem,
        string $conditionStatus,
        ?string $conditionNote
    ): void {
        $distributionItem->update([
            'status' => 'dikembalikan',
            'returned_at' => now(),
            'return_condition_status' => $conditionStatus,
            'return_note' => $conditionNote,
        ]);
    }

    private function syncReturnedItemStatus(
        Items $item,
        string $conditionStatus,
        int $storageLocationId,
        ?string $conditionNote
    ): void {
        if ($conditionStatus === 'maintenance') {
            $item->update([
                'status' => 'maintenance',
                'storage_location_id' => $storageLocationId,
                'condition_note' => $conditionNote,
            ]);

            return;
        }

        $stillUsed = $item->distributionItems()
            ->where('status', 'dipakai')
            ->whereHas('distribution', function($q) {
                $q->where('status', 'dipakai');
            })
            ->exists();

        $item->update([
            'status' => $stillUsed ? 'used' : 'available',
            'storage_location_id' => $stillUsed ? null : $storageLocationId,
            'condition_note' => null,
        ]);
    }

    private function closeDistributionIfNoActiveItems(?Distribution $distribution): void
    {
        if (!$distribution) {
            return;
        }

        $hasActiveItems = $distribution->distributionItems()
            ->where('status', 'dipakai')
            ->exists();

        if (!$hasActiveItems) {
            $distribution->update([
                'status' => 'dikembalikan'
            ]);
        }
    }

    private function isSharedPrinter(Items $item): bool
    {
        return in_array($item->kategori, ['Printer Kertas', 'Printer Barcode']);
    }

    public function reportDetail(Request $request)
    {
        $reports = $this->reportDetailQuery($request)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('distribution.report_detail', compact('reports'));
    }

    public function exportReportDetail(Request $request, string $format)
    {
        $reports = $this->reportDetailQuery($request)
            ->latest()
            ->get();

        $filename = 'laporan-detail-distribusi-' . now()->format('Ymd-His');

        if ($format === 'excel') {
            return response()->streamDownload(function () use ($reports) {
                echo view('distribution.report_detail_excel', [
                    'reports' => $reports,
                ])->render();
            }, $filename . '.xls', [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            ]);
        }

        if ($format === 'pdf') {
            return view('distribution.report_detail_pdf', [
                'reports' => $reports,
            ]);
        }

        abort(404);
    }

    private function reportDetailQuery(Request $request)
    {
        $query = Distribution::with([
            'location',
            'distributionItems.item.device_detail',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_distribusi', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_distribusi', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('gedung')) {
            $query->whereHas('location', function ($location) use ($request) {
                $location->where('gedung', $request->gedung);
            });
        }

        if ($request->filled('ruangan')) {
            $query->where('location_id', $request->ruangan);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_user', 'like', "%{$request->search}%")
                    ->orWhere('divisi', 'like', "%{$request->search}%")
                    ->orWhereHas('distributionItems.item', function ($item) use ($request) {
                        $item->where('serial_number', 'like', "%{$request->search}%")
                            ->orWhere('merk', 'like', "%{$request->search}%")
                            ->orWhere('type', 'like', "%{$request->search}%");
                    });
            });
        }

        return $query;
    }
}
