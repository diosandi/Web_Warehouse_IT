<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use App\Models\Items;
use App\Models\DistributionItem;
use App\Models\Locations;
use Illuminate\Http\Request;

class DistributionController extends Controller
{
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

        $suggestions = $results->map(function($d) {
        $item = $d->distributionItems->first()?->item;
        $location = $d->location;

        $itemText = $item
            ? ($item->serial_number . ' - ' . $item->merk)
            : '-';

        $locationText = $location
            ? ($location->gedung . ' - ' . $location->ruangan)
            : '-';


            return [
                'id' => $d->id,
                'merk' => $item ? $item->merk : '-',
                'serial_number' => $item ? $item->serial_number : '-',
                'gedung'=> $location ? $location->gedung : '-',
                'ruangan'=>$location ? $location->ruangan : '-',
                'nama_user' => $d->nama_user,
                'text' => trim($itemText .' | Lokasi: '. $locationText . ' | Nama User: ' . ($d->nama_user ?? '-'))
            ];
        });

        return response()->json($suggestions);
    }
    //Search dapat ruangan
    public function getRuangan(Request $request)
    {
        $ruangan = Locations::where('gedung', $request->gedung)->get();

        return response()->json($ruangan);
    }

    public function index(Request $request)
    {
        $query = Distribution::with(['distributionItems.item', 'location']);
        // SEARCH
        if ($request->search) {
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
        if ($request->location_id) {
            $query->where('location_id', $request->location_id);
        }

        $distribution = $query->latest()->paginate(10)->withQueryString();

        $locations = Locations::all();

        return view('distribution.index', compact('distribution', 'locations'));
    }

    public function create()
    {
        $gedungList = Locations::select('gedung')
        ->distinct()
        ->pluck('gedung');

        return view('distribution.create', [
            'pcs' => Items::where('kategori','PC')->where('status','available')->get(),
            'monitors' => Items::where('kategori','Monitor')->where('status','available')->get(),
            'printers_kertas' => Items::where('kategori','Printer Kertas')->where('status','available')->get(),
            'printers_barcode' => Items::where('kategori','Printer Barcode')->where('status','available')->get(),
            'scanners' => Items::where('kategori','Scanner')->where('status','available')->get(),
            'locations' => Locations::all(),
            'gedungList' => $gedungList
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'location_id' => 'required',
            'nama_user' => 'nullable',
            'divisi' => 'nullable',
            'tanggal_distribusi' => 'required|date',
            'items' => 'nullable|array',
            'items.*' => 'nullable|exists:items,id',
            'printer_kertas_ids' => 'nullable|array',
            'printer_kertas_ids.*' => 'exists:items,id',
            'printer_barcode_ids' => 'nullable|array',
            'printer_barcode_ids.*' => 'exists:items,id',
        ]);

        $items = collect($request->input('items', []))
            ->merge($request->input('printer_kertas_ids', []))
            ->merge($request->input('printer_barcode_ids', []))
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

            if (in_array($item->kategori, ['PC', 'Monitor', 'Scanner'])) {

                $dipakai = DistributionItem::where('item_id', $itemId)
                    ->whereHas('distribution', function($q) {
                        $q->where('status', 'dipakai');
                    })
                    ->exists();

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

            // kalau bukan printer → set used
            if (!in_array($item->kategori, ['Printer Kertas', 'Printer Barcode'])) {
                $item->update(['status' => 'used']);
            }
        }
        return redirect()->route('distribution.index')
            ->with('success', 'Distribusi berhasil disimpan');
    }

    public function edit($id)
    {
        $distribution = Distribution::with('distributionItems.item')->findOrFail($id);

        $selectedItems = $distribution->distributionItems->pluck('item_id')->toArray();

        $pc_selected = $distribution->distributionItems
        ->where('item.kategori', 'PC')
        ->first()?->item;
        
        $monitor_selected = $distribution->distributionItems
        ->where('item.kategori', 'Monitor')
        ->first()?->item;

        $printer_kertas_selected = $distribution->distributionItems
        ->filter(fn ($distributionItem) => optional($distributionItem->item)->kategori === 'Printer Kertas')
        ->pluck('item');

        $printer_barcode_selected = $distribution->distributionItems
        ->filter(fn ($distributionItem) => optional($distributionItem->item)->kategori === 'Printer Barcode')
        ->pluck('item');

        $scanner_selected = $distribution->distributionItems
        ->where('item.kategori', 'Scanner')
        ->first()?->item;

        $pcs = Items::where('kategori','PC')
            ->where(function($q) use ($selectedItems){
                $q->where('status','available')
                ->orWhereIn('id', $selectedItems);
            })->get();

        $monitors = Items::where('kategori','Monitor')
            ->where(function($q) use ($selectedItems){
                $q->where('status','available')
                ->orWhereIn('id', $selectedItems);
            })->get();

        $printers_kertas = Items::where('kategori','Printer Kertas')
            ->where(function($q) use ($selectedItems){
                $q->where('status','available')
                ->orWhereIn('id', $selectedItems);
            })->get();

        $printers_barcode = Items::where('kategori','Printer Barcode')
            ->where(function($q) use ($selectedItems){
                $q->where('status','available')
                ->orWhereIn('id', $selectedItems);
            })->get();

        $scanners = Items::where('kategori','Scanner')
            ->where(function($q) use ($selectedItems){
                $q->where('status','available')
                ->orWhereIn('id', $selectedItems);
            })->get();

        $gedungList = Locations::select('gedung')
        ->distinct()
        ->pluck('gedung');

        $locations = Locations::all();

        return view('distribution.edit', compact(
            'distribution',
            'selectedItems',
            'pcs','monitors','printers_kertas','printers_barcode','scanners',
            'locations',
            'gedungList',
            'pc_selected',
            'monitor_selected',
            'printer_kertas_selected',
            'printer_barcode_selected',
            'scanner_selected'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'location_id' => 'required',
            'tanggal_distribusi' => 'required|date',

            // TAMBAHAN
            'items' => 'nullable|array',
            'items.*' => 'nullable|exists:items,id',
            'printer_kertas_ids' => 'nullable|array',
            'printer_kertas_ids.*' => 'exists:items,id',
            'printer_barcode_ids' => 'nullable|array',
            'printer_barcode_ids.*' => 'exists:items,id',
        ]);
        $distribution = Distribution::findOrFail($id);

        $items = collect($request->input('items', []))
            ->merge($request->input('printer_kertas_ids', []))
            ->merge($request->input('printer_barcode_ids', []))
            ->filter()
            ->unique()
            ->values();

        if ($items->isEmpty()) {
            return back()->with(['error' => 'Minimal pilih 1 device!'])->withInput();
        }

        // CEK KHUSUS DEVICE EXCLUSIVE, kecuali device yang memang sudah ada di distribusi ini
        foreach ($items as $itemId) {
            $item = Items::find($itemId);

            if (in_array($item->kategori, ['PC', 'Monitor', 'Scanner'])) {
                $dipakai = DistributionItem::where('item_id', $itemId)
                    ->where('distribution_id', '!=', $distribution->id)
                    ->whereHas('distribution', function($q) {
                        $q->where('status', 'dipakai');
                    })
                    ->exists();

                if ($dipakai) {
                    return back()->with('error', $item->serial_number . ' sudah dipakai!')->withInput();
                }
            }
        }

        // ambil item lama
        $oldItems = DistributionItem::where('distribution_id', $id)->get();

        foreach ($oldItems as $old) {

            $item = Items::find($old->item_id);

            // hanya non-printer yang direset
            if (!in_array($item->kategori, ['Printer Kertas', 'Printer Barcode'])) {
                $item->update(['status' => 'available']);
            }

            $old->delete();
        }

        $status = $items->isNotEmpty() ? 'dipakai' : 'dikembalikan';
        // update header
        $distribution->update([
            'location_id' => $request->location_id,
            'nama_user' => $request->nama_user,
            'divisi' => $request->divisi,
            'tanggal_distribusi' => $request->tanggal_distribusi,
            'keterangan' => $request->keterangan,
            'status' => $status,
        ]);

        // insert ulang item
        foreach ($items as $itemId) {
            DistributionItem::create([
                'distribution_id' => $distribution->id,
                'item_id' => $itemId
            ]);

            $item = Items::find($itemId);

            if (!in_array($item->kategori, ['Printer Kertas', 'Printer Barcode'])) {
                $item->update(['status' => 'used']);
            }
        }

        return redirect()->route('distribution.index')
            ->with('success', 'Distribusi berhasil diupdate');
    }

    public function destroy($id)
    {
        $usedCount = DistributionItem::where('item_id', $id)->count();

        if ($usedCount == 0) {
            Items::where('id', $id)->update([
                'status' => 'available'
            ]);
        }

        $distribution=Distribution::findOrFail($id);
        //Ubah status
        $distribution->update([
            'status' => 'dikembalikan'
        ]);
        //kembali item ke gudang
        foreach ($distribution->distributionItems as $d) {

            $item = $d->item;

            if (!in_array($item->kategori, ['Printer Kertas', 'Printer Barcode'])) {
                $item->update(['status' => 'available']);
            }
        }

        return back()->with('succes','Barang berhasil dikembalikan');
    }
}
