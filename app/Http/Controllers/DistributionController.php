<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use App\Models\Items;
use App\Models\DistributionItem;
use App\Models\Locations;
use Illuminate\Http\Request;

class DistributionController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->q;
        $kategori = $request->kategori;

        $items = Items::where('kategori', $kategori)
            ->where('status', 'available')
            ->where(function($q) use ($query) {
                $q->where('serial_number', 'like', "%$query%")
                ->orWhere('merk', 'like', "%$query%");
            })
            ->limit(10)
            ->get();

        return response()->json($items);
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

        // $distribution = Distribution::with(['distributionItems.item', 'location'])
        // ->latest()
        // ->paginate(10);

        // return view('distribution.index', compact('distribution'));
    }

    public function create()
    {
        return view('distribution.create', [
            'pcs' => Items::where('kategori','PC')->where('status','available')->get(),
            'monitors' => Items::where('kategori','Monitor')->where('status','available')->get(),
            'printers_kertas' => Items::where('kategori','Printer Kertas')->where('status','available')->get(),
            'printers_barcode' => Items::where('kategori','Printer Barcode')->where('status','available')->get(),
            'scanners' => Items::where('kategori','Scanner')->where('status','available')->get(),
            'locations' => Locations::all(),
        ]);
    }

    public function store(Request $request)
    {
            $request->validate([
            'location_id' => 'required',
            'nama_user' => 'nullable',
            'divisi' => 'nullable',
            'tanggal_distribusi' => 'required|date',
            'items' => 'required|array',
            'items.*' => 'nullable|exists:items,id'
        ]);

        // CEK MINIMAL 1 DEVICE
            if (!collect($request->items)->filter()->count()) {
                return back()->withErrors(['items' => 'Minimal pilih 1 device!']);
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
        foreach ($request->items as $itemId) {

            if (!$itemId) continue;

            DistributionItem::create([
                'distribution_id' => $distribution->id,
                'item_id' => $itemId
            ]);

            // update status item
            Items::where('id', $itemId)
                ->update(['status' => 'used']);
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
        ->where('item.kategori', 'Printer Kertas')
        ->first()?->item;

        $printer_barcode_selected = $distribution->distributionItems
        ->where('item.kategori', 'Printer Barcode')
        ->first()?->item;

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

        $locations = Locations::all();

        return view('distribution.edit', compact(
            'distribution',
            'selectedItems',
            'pcs','monitors','printers_kertas','printers_barcode','scanners',
            'locations',
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
            'items' => 'required|array',
            'items.*' => 'nullable|exists:items,id'
        ]);
        $distribution = Distribution::findOrFail($id);

        // ambil item lama
        $oldItems = DistributionItem::where('distribution_id', $id)->get();

        foreach ($oldItems as $old) {
            // balikin status
            Items::where('id', $old->item_id)
                ->update(['status' => 'available']);

            $old->delete();
        }

        $items = array_filter($request->items ?? []); // buang yang kosong

        if (count($items) == 0) {
            return back()->withErrors(['items' => 'Minimal pilih 1 device!']);
        }

        $status = count($items) > 0 ? 'dipakai' : 'dikembalikan';
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
        foreach ($request->items as $itemId) {

            if (!$itemId) continue;

            DistributionItem::create([
                'distribution_id' => $distribution->id,
                'item_id' => $itemId
            ]);

            Items::where('id', $itemId)
                ->update(['status' => 'used']);
        }

        return redirect()->route('distribution.index')
            ->with('success', 'Distribusi berhasil diupdate');
    }

    public function destroy($id)
    {
    // $distribution = Distribution::findOrFail($id);

    // foreach ($distribution->items as $di) {
    //     $di->item->update(['status' => 'available']);
    // }

    // $distribution->update(['status' => 'dikembalikan']);

    // return back()->with('success', 'Barang berhasil dikembalikan');
        $distribution=Distribution::findOrFail($id);
        //Ubah status
        $distribution->update([
            'status' => 'dikembalikan'
        ]);
        //kembali item ke gudang
        foreach ($distribution->distributionItems as $d) {
            $d->item->update(['status' => 'available']);
        }

        return back()->with('succes','Barang berhasil dikembalikan');
    }
}
