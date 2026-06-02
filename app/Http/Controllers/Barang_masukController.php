<?php

namespace App\Http\Controllers;

use App\Models\Barang_masuk;
use App\Models\Items;
use App\Models\SerialNumberCorrection;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Concerns\ResolvesRedirects;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class Barang_masukController extends Controller
{
    use ResolvesRedirects;

    public function searchBarangMasuk(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        $search = "%{$q}%";
        $results = Barang_masuk::with('items')
            ->where('supplier', 'like', $search)
            ->orWhere('po_number', 'like', $search)
            ->orWhere('keterangan', 'like', $search)
            ->orWhereHas('items', function ($itemQuery) use ($search) {
                $itemQuery->where('merk', 'like', $search)
                          ->orWhere('type', 'like', $search)
                          ->orWhere('kategori', 'like', $search);
            })
            ->limit(10)
            ->get();

        $suggestions = $results->map(function($d) {
            $item = $d->items->first();
            $itemText = $item
                ? ($item->merk . ' - ' . $item->type . ' - ' . $item->kategori)
                : '-';

            return [
                'id' => $d->id,
                'merk' => $item ? $item->merk : '-',
                'type' => $item ? $item->type : '-',
                'kategori' => $item ? $item->kategori : '-',
                'supplier' => $d->supplier,
                'po_number' => $d->po_number,
                'text' => trim($itemText . ' | supplier: ' . ($d->supplier ?? '-'))
            ];
        });

        return response()->json($suggestions);
    }

    public function index(Request $request)
    {
        $query = $this->barangMasukQuery($request);

         $barang_masuk = $query->latest()->paginate(10)->withQueryString();
        // $barang_masuk = Barang_masuk::with('items')->paginate(10);
        // $barangMasuk = collect();
        return view('barang_masuk.index',compact('barang_masuk'));
    }

    public function export(Request $request, string $format)
    {
        $barangMasuk = $this->barangMasukQuery($request)
            ->latest()
            ->get();

        $data = [
            'barangMasuk' => $barangMasuk,
            'periodeLabel' => $this->periodeLabel($request),
            'filterLabel' => $this->filterLabel($request),
        ];

        $filename = 'laporan-barang-masuk-' . now()->format('Ymd-His');

        if ($format === 'excel') {
            return response()->streamDownload(function () use ($data) {
                echo view('barang_masuk.export_excel', $data)->render();
            }, $filename . '.xls', [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            ]);
        }

        if ($format === 'pdf') {
            return view('barang_masuk.export_pdf', $data);
        }

        abort(404);
    }

    private function barangMasukQuery(Request $request)
    {
        $query = Barang_masuk::with(['items.storageLocation']);

        // SEARCH
        if ($request->filled('item_id')) {
            $query->whereKey($request->item_id);
        } elseif ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('supplier', 'like', "%{$request->search}%")
                ->orWhere('po_number', 'like', "%{$request->search}%")
                ->orWhere('keterangan', 'like', "%{$request->search}%")
                ->orWhereHas('items', function($item) use ($request) {
                    $item->where('serial_number', 'like', "%{$request->search}%")
                        ->orWhere('service_tag', 'like', "%{$request->search}%")
                        ->orWhere('kategori', 'like', "%{$request->search}%")
                        ->orWhere('merk', 'like', "%{$request->search}%")
                        ->orWhere('type', 'like', "%{$request->search}%");
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

        return $query;
    }

    private function periodeLabel(Request $request): string
    {
        if ($request->tanggal_dari && $request->tanggal_sampai) {
            return $request->tanggal_dari . ' sampai ' . $request->tanggal_sampai;
        }

        if ($request->tanggal_dari) {
            return 'Mulai ' . $request->tanggal_dari;
        }

        if ($request->tanggal_sampai) {
            return 'Sampai ' . $request->tanggal_sampai;
        }

        return 'Semua periode';
    }

    private function filterLabel(Request $request): string
    {
        $filters = [];

        if ($request->filled('search')) {
            $filters[] = 'Cari: ' . $request->search;
        }

        if ($request->filled('kategori')) {
            $filters[] = 'Kategori: ' . $request->kategori;
        }

        if ($request->filled('item_id')) {
            $filters[] = 'Pilihan suggestion';
        }

        return empty($filters) ? 'Semua data' : implode(' | ', $filters);
    }

    public function create(Request $request)
    {
        $redirect = $this->redirectTarget($request, route('barang_masuk.index'));
        return view('barang_masuk.create', compact('redirect'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori'=> 'required',
            'merk'=> 'required',
            'type'=> 'required',
            'serial_numbers'=>'required|array',
            'serial_numbers.*' => 'required|distinct|unique:items,serial_number'
        ],[
            'serial_numbers.*.required' => 'Serial Number tidak boleh kosong',
            'serial_numbers.*.unique' => 'Serial Number harus unik',
            'serial_numbers.*.distinct' => 'Serial Number tidak boleh duplicate',
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
                'type'=>$request->type,
                'kategori'=>$request->kategori,
                'status'=>'available'
            ]);
        }
        return redirect($this->redirectTarget($request, route('barang_masuk.index')))->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit(Request $request, $id)
    {
        $barang_masuk=Barang_masuk::with('items')->findOrFail($id);
        $items = $barang_masuk->items; // relasi hasMany
        $redirect = $this->redirectTarget($request, route('barang_masuk.index'));
        return view('barang_masuk.edit',compact('barang_masuk','items', 'redirect'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
           'kategori'=>'required',
           'merk'=>'required',
           'type'=>'required',
           'serial_numbers'=>'required|array',
           'serial_numbers.*' => 'required|distinct'
        ],[
            'serial_numbers.*.required' => 'Serial Number tidak boleh kosong',
            // 'serial_numbers.*.unique' => 'Serial Number harus unik',
            'serial_numbers.*.distinct' => 'Serial Number tidak boleh duplicate',
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
        // $oldItems=Items::where('barang_masuk_id',$id)->get();
        // foreach($oldItems as $old){
        //     $old->delete();
        // }

        //Update Header
        $barang_masuk->update([
            'supplier' => $request->supplier,
            'tanggal_masuk' => $request->tanggal_masuk,
            'po_number'=>$request->po_number,
            'quantity' => count($request->serial_numbers),
            'keterangan' => $request->keterangan
        ]);

        // simpan id item yang masih ada
        $existingIds = [];

        foreach($request->serial_numbers as $index => $sn){

            $itemId = $request->item_ids[$index] ?? null;

            if($itemId){

                // update item lama
                $item = Items::find($itemId);

                if($item){

                    $item->update([
                        'serial_number' => $sn,
                        'merk' => $request->merk,
                        'type' => $request->type,
                        'kategori' => $request->kategori,
                    ]);

                    $existingIds[] = $item->id;
                }

            } else {

                // tambah item baru
                $newItem = Items::create([
                    'barang_masuk_id' => $barang_masuk->id,
                    'serial_number' => $sn,
                    'merk' => $request->merk,
                    'type' => $request->type,
                    'kategori' => $request->kategori,
                    'status' => 'available'
                ]);

                $existingIds[] = $newItem->id;
            }
        }

        // hapus item yang dihapus dari form
        Items::where('barang_masuk_id', $barang_masuk->id)
            ->whereNotIn('id', $existingIds)
            ->delete();


        //Simpan Ulang Item
        // foreach($request->serial_numbers as $sn){
        //     Items::create([
        //         'barang_masuk_id' => $barang_masuk->id,
        //         'serial_number'=>$sn,
        //         'merk'=>$request->merk,
        //         'type'=>$request->type,
        //         'kategori'=>$request->kategori,
        //         'status'=>'available'
        //     ]);
        // }
        return redirect($this->redirectTarget($request, route('barang_masuk.index')))->with('success', 'Barang berhasil diperbarui!');
    }

    public function koreksiSn(Request $request, $id)
    {
        $barang_masuk = Barang_masuk::with(['items' => function ($query) {
            $query->orderBy('serial_number');
        }])->findOrFail($id);

        $corrections = SerialNumberCorrection::with(['item', 'user'])
            ->where('barang_masuk_id', $barang_masuk->id)
            ->latest()
            ->get();

        $redirect = $this->redirectTarget($request, route('barang_masuk.index'));

        return view('barang_masuk.koreksi_sn', compact('barang_masuk', 'corrections', 'redirect'));
    }

    public function updateKoreksiSn(Request $request, $id)
    {
        $validated = $request->validate([
            'serial_numbers' => 'required|array',
            'serial_numbers.*' => 'required|string|max:255',
            'reason' => 'required|string|max:1000',
        ], [
            'serial_numbers.*.required' => 'Serial Number tidak boleh kosong',
            'reason.required' => 'Alasan koreksi wajib diisi',
        ]);

        $barang_masuk = Barang_masuk::with('items')->findOrFail($id);
        $items = $barang_masuk->items()->get()->keyBy('id');
        $seenSerialNumbers = [];
        $changes = [];

        foreach ($validated['serial_numbers'] as $itemId => $serialNumber) {
            $itemId = (int) $itemId;
            $newSerialNumber = trim($serialNumber);

            if (!$items->has($itemId)) {
                throw ValidationException::withMessages([
                    'serial_numbers' => 'Item tidak valid untuk barang masuk ini.',
                ]);
            }

            $serialKey = strtolower($newSerialNumber);
            if (isset($seenSerialNumbers[$serialKey])) {
                throw ValidationException::withMessages([
                    'serial_numbers' => 'Serial Number tidak boleh duplicate.',
                ]);
            }
            $seenSerialNumbers[$serialKey] = true;

            $item = $items->get($itemId);

            if ($newSerialNumber === $item->serial_number) {
                continue;
            }

            $serialExists = Items::where('serial_number', $newSerialNumber)
                ->where('id', '!=', $item->id)
                ->exists();

            if ($serialExists) {
                throw ValidationException::withMessages([
                    'serial_numbers' => "Serial Number {$newSerialNumber} sudah digunakan item lain.",
                ]);
            }

            $changes[] = [
                'item' => $item,
                'old_serial_number' => $item->serial_number,
                'new_serial_number' => $newSerialNumber,
            ];
        }

        if (empty($changes)) {
            return back()->withInput()->with('error', 'Tidak ada Serial Number yang berubah.');
        }

        DB::transaction(function () use ($changes, $barang_masuk, $validated) {
            foreach ($changes as $change) {
                $item = $change['item'];

                $item->update([
                    'serial_number' => $change['new_serial_number'],
                ]);

                SerialNumberCorrection::create([
                    'item_id' => $item->id,
                    'barang_masuk_id' => $barang_masuk->id,
                    'user_id' => Auth::id(),
                    'old_serial_number' => $change['old_serial_number'],
                    'new_serial_number' => $change['new_serial_number'],
                    'reason' => $validated['reason'],
                ]);
            }
        });

        return redirect($this->redirectTarget($request, route('barang_masuk.index')))
            ->with('success', 'Koreksi Serial Number berhasil disimpan.');
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

        return redirect($this->redirectTarget(request(), route('barang_masuk.index')))->with('success', 'Data barang masuk berhasil dihapus');
    }
}
