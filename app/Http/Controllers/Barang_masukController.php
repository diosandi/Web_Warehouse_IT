<?php

namespace App\Http\Controllers;

use App\Models\Barang_masuk;
use App\Models\Items;
use Illuminate\Http\Request;

class Barang_masukController extends Controller
{

    public function searchBarangMasuk(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        $search = "%{$q}%";
        $results = Barang_masuk::with('items')
            ->where('supplier', 'like', $search)
            ->orWhere('keterangan', 'like', $search)
            ->orWhereHas('items', function ($itemQuery) use ($search) {
                $itemQuery->where('merk', 'like', $search)
                          ->orWhere('kategori', 'like', $search);
            })
            ->limit(10)
            ->get();

        $suggestions = $results->map(function($d) {
            $item = $d->items->first();
            $itemText = $item
                ? ($item->merk . ' - ' . $item->kategori)
                : '-';

            return [
                'id' => $d->id,
                'merk' => $item ? $item->merk : '-',
                'kategori' => $item ? $item->kategori : '-',
                'supplier' => $d->supplier,
                'text' => trim($itemText . ' | supplier: ' . ($d->supplier ?? '-'))
            ];
        });

        return response()->json($suggestions);
    }

    public function index(Request $request)
    {
        $query = Barang_masuk::with(['items']);
        // SEARCH
        if ($request->filled('item_id')) {
        $query->whereKey($request->item_id);
        } elseif ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('supplier', 'like', "%{$request->search}%")
                ->orWhere('keterangan', 'like', "%{$request->search}%")
                ->orWhereHas('items', function($item) use ($request) {
                    $item->where('serial_number', 'like', "%{$request->search}%")
                        ->orWhere('kategori', 'like', "%{$request->search}%")
                        ->orWhere('merk', 'like', "%{$request->search}%");
                });
            });
        }

        // FILTER KATEGORI
        if ($request->kategori) {
            $query->whereHas('items', function($q) use ($request){
                $q->where('kategori',$request->kategori);
            });
        }

         // filter tanggal range
        if ($request->tanggal_dari && $request->tanggal_sampai) {
            $query->whereBetween('tanggal_masuk', [
                $request->tanggal_dari,
                $request->tanggal_sampai
            ]);
        }

        // kalau cuma dari aja
        if ($request->tanggal_dari && !$request->tanggal_sampai) {
            $query->whereDate('tanggal_masuk', '>=', $request->tanggal_dari);
        }

        // kalau cuma sampai aja
        if (!$request->tanggal_dari && $request->tanggal_sampai) {
            $query->whereDate('tanggal_masuk', '<=', $request->tanggal_sampai);
        }

         $barang_masuk = $query->latest()->paginate(10)->withQueryString();
        // $barang_masuk = Barang_masuk::with('items')->paginate(10);
        // $barangMasuk = collect();
        return view('barang_masuk.index',compact('barang_masuk'));
    }

    public function create(Request $request)
    {
        return view('barang_masuk.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori'=> 'required',
            'merk'=> 'required',
            'serial_numbers'=>'required|array'
        ]);

        //simpan header
        $barang_masuk = Barang_masuk::create([
            'supplier' => $request->supplier,
            'tanggal_masuk' => $request->tanggal_masuk,
            'po_number'=>$request->po_number,
            'quantity' => count($request->serial_numbers),
            'keterangan' => $request->keterangan,
        ]);

        //simpan item
        foreach($request->serial_numbers as $sn){
            Items::create([
                'barang_masuk_id' => $barang_masuk->id,
                'serial_number'=>$sn,
                'merk'=>$request->merk,
                'kategori'=>$request->kategori,
                'status'=>'available'
            ]);
        }
        return redirect()->route('barang_masuk.index')->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit($id)
    {
        $barang_masuk=Barang_masuk::with('items')->findOrFail($id);
        $items = $barang_masuk->items; // relasi hasMany
        return view('barang_masuk.edit',compact('barang_masuk','items'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
           'kategori'=>'required',
           'merk'=>'required',
           'serial_numbers'=>'required|array'
        ]);

        $barang_masuk = Barang_masuk::findOrfail($id);
        //Cek apakah item sudah dipakai
        $usedItems = Items::where('barang_masuk_id', $id)
            ->where('status','used')
            ->exists();

        if($usedItems){
            return back()->with('error','Tidak bisa edit, ada item yang sudah dipakai');
        }
        //Hapus Item lama
        $oldItems=Items::where('barang_masuk_id',$id)->get();
        foreach($oldItems as $old){
            $old->delete();
        }

        //Update Header
        $barang_masuk->update([
            'supplier' => $request->supplier,
            'tanggal_masuk' => $request->tanggal_masuk,
            'po_number'=>$request->po_number,
            'quantity' => count($request->serial_numbers),
            'keterangan' => $request->keterangan
        ]);

        //Simpan Ulang Item
        foreach($request->serial_numbers as $sn){
            Items::create([
                'barang_masuk_id' => $barang_masuk->id,
                'serial_number'=>$sn,
                'merk'=>$request->merk,
                'kategori'=>$request->kategori,
                'status'=>'available'
            ]);
        }
        return redirect()->route('barang_masuk.index')->with('success', 'Barang berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $barang_masuk = Barang_masuk::findOrFail($id);
        // Cek apakah item sudah dipakai
        $usedItems = Items::where('barang_masuk_id', $id)
            ->where('status','used')
            ->exists();

        if($usedItems){
            return back()->with('error','Tidak bisa hapus, ada item yang sudah dipakai');
        }

        // hapus semua item terkait
        Items::where('barang_masuk_id', $id)->delete();

        // hapus header
        $barang_masuk->delete();

        return redirect()->route('barang_masuk.index')->with('success', 'Data barang masuk berhasil dihapus');
    }
}
