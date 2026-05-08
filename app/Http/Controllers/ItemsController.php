<?php

namespace App\Http\Controllers;

use App\Models\Items;
use App\Models\DistributionItem;
use App\Models\Device_details;
use App\Models\Locations;
use Illuminate\Http\Request;

class ItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function searchItems(Request $request)
    {
        $q = $request->q;

        $items = Items::where('serial_number', 'like', "%$q%")
            ->orWhere('service_tag', 'like', "%$q%")
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
                    'text' => $item->serial_number . ' - ' . $item->merk . '-' . $item->type
                ];
            })
        );
    }

    public function index(Request $request)
    {
        $query = Items::query();
        $selectedKategori = array_values(array_filter((array) $request->input('kategori', '')));
        $selectedMerk = array_values(array_filter((array) $request->input('merk', '')));

         if ($request->item_id) {
            // 🔥 kalau pilih dari suggestion → pakai ID saja
            $query->where('id', $request->item_id);
        }
            elseif ($request->filled('search')) {
                // 🔥 kalau manual ketik → pakai search
                $search = '%' . $request->search . '%';
                $query->where(function ($q) use ($search) {
                $q->where('serial_number', 'like', $search)
                  ->orWhere('service_tag', 'like', $search)
                  ->orWhere('merk', 'like', $search)
                  ->orWhere('type', 'like', $search)
                  ->orWhere('processor', 'like', $search)
                  ->orWhere('os', 'like', $search)
                  ->orWhere('ram_gb', 'like', $search)
                  ->orWhere('tahun','like', $search);
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

        // Filter by status
        // if ($request->status) {
        //     $query->where('status', $request->status == 'digunakan' ? 'used' : 'available');
        // }

        if ($request->status) {

            $query->where(function($q) use ($request) {

                // PRINTER
                $q->where(function($q2) use ($request) {
                    $q2->whereIn('kategori', ['Printer Kertas', 'Printer Barcode']);

                    if ($request->status == 'used') {
                        $q2->whereHas('distributionItems.distribution', function($d) {
                            $d->where('status', 'dipakai');
                        });
                    }

                    if ($request->status == 'available') {
                        $q2->whereDoesntHave('distributionItems.distribution', function($d) {
                            $d->where('status', 'dipakai');
                        });
                    }
                })

                // NON PRINTER
                ->orWhere(function($q2) use ($request) {
                    $q2->whereNotIn('kategori', ['Printer Kertas', 'Printer Barcode'])
                    ->where('status', $request->status);
                });

            });

        }

        $items = $query->latest()->paginate(10)->appends($request->query());

        // Get all kategori options
        $kategoriOptions = Items::getKategoriOptions();

        // Get distinct merk list
        $merkList = Items::select('merk')
            ->distinct()
            ->orderBy('merk')
            ->pluck('merk')
            ->toArray();

        // Get current filters for display
        $filters = [
            'kategori' => $selectedKategori,
            'merk' => $selectedMerk,
            'search' => $request->search
        ];

        return view('items.index', compact('items', 'kategoriOptions', 'merkList', 'filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategoriOptions = Items::getKategoriOptions();
        $locations = Locations::where('type', 'warehouse')->get();

        return view('items.create', compact('kategoriOptions','locations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|in:PC,Monitor,Printer Kertas,Printer Barcode,Scanner','Lainnya',
            'merk' => 'required|string|max:255',
            'type' => 'required|string|max:255',
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

        return redirect()->route('items.index')->with('success', 'Master Data Barang berhasil ditambahkan !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Items $item)
    {
            $item->load([
                'device_detail',
                'distributionItems.distribution.location'
            ]);

            return view('items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Items $item)
    {
        $kategoriOptions = Items::getKategoriOptions();
        $locations = Locations::where('type', 'warehouse')->get();
        return view('items.edit', compact('item', 'kategoriOptions', 'locations'));
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
            'serial_number' => 'required|string|unique:items,serial_number,' . $item->id,
            'service_tag' => 'nullable|string|max:255',
            'processor' => 'nullable|string|max:255',
            'ram_gb' => 'nullable|integer|min:1',
            'storage_gb' => 'nullable|integer|min:1',
            'vga' => 'nullable|string|max:255',
            'os' => 'nullable|string|max:255',
            'tahun' => 'nullable|digits:4',
            'storage_location_id' => 'nullable|exists:locations,id',

        ]);
        // $item->update([
        //     'is_active' => false
        // ]);
        // Items::where('is_active', true)->get();
        // HANDLE MERK DI SINI
        if ($item->barang_masuk_id) {
            $validated['merk'] = $item->merk; // paksa pakai yang lama
        }

        $item->update($validated);

        return redirect()->route('items.index')->with('success', 'Master Data Barang berhasil diperbarui !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = Items::findOrfail($id);

        // Cek apakah item sedang dipakai
        if ($item->status=='used'){
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

        // Update quantity
        $barangMasuk->quantity = $barangMasuk->items()->count();
        $barangMasuk->save();


        return redirect()->route('items.index')->with('success', 'Item berhasil dihapus !');
    }
}
