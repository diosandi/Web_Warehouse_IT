<?php

namespace App\Http\Controllers;

use App\Models\Items;
use App\Models\DistributionItem;
use App\Models\Locations;
use App\Http\Controllers\Concerns\ResolvesRedirects;
use Illuminate\Http\Request;
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
                    'text' => $item->serial_number . ' - ' . $item->merk . ' - ' . $item->type
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
            // kalau pilih dari suggestion → pakai ID saja
            $query->where('id', $request->item_id);
        }
            elseif ($request->filled('search')) {
                // kalau manual ketik → pakai search
                $search = '%' . $request->search . '%';
                $query->where(function ($q) use ($search) {
                $q->where('serial_number', 'like', $search)
                  ->orWhere('service_tag', 'like', $search)
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

       if ($request->status) {
            $activeDistribution = function ($di) {
                $di->where('status', 'dipakai')
                    ->whereHas('distribution', function ($d) {
                        $d->where('status', 'dipakai');
                    });
            };

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

                // available / maintenance / retired harus sesuai status yang tampil di tabel.
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
    public function create(Request $request)
    {
        $kategoriOptions = Items::getKategoriOptions();
        $locations = Locations::where('type', 'warehouse')->get();
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
        $item->load([
        'storageLocation',
        'barang_masuk',
        ]);

        $locations = Locations::where('type', 'warehouse')->get();
        $redirect = $this->redirectTarget($request, route('items.index'));
        return view('items.detail', compact('item','locations','redirect'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Items $item, Request $request)
    {
            $item->load([
                'device_detail',
                'storageLocation',
                'distributionItems.distribution.location'
            ]);

            $activeDistributions = $item->distributionItems()
                ->with(['distribution.location'])
                ->where('status', 'dipakai')
                ->latest()
                ->get();

            $activeDistributionItem = $item->distributionItems() 
            ->with([ 'distribution.location' ]) 
            ->where('status', 'dipakai') 
            ->latest() 
            ->first();

            $historyDistributions = $this->historyDistributionQuery($item, $request)
                ->paginate((int) $request->input('history_per_page', 10), ['*'], 'history_page')
                ->withQueryString();

            $locations = Locations::where('type', 'warehouse')->get();
            $historyFilters = $this->historyFilters($request);

            $redirect = $this->redirectTarget($request, route('items.index'));
            
            return view('items.show', compact(
            'item',
            'historyDistributions',
            'activeDistributions',
            'activeDistributionItem',
            'redirect',
            'locations',
            'historyFilters'
            ));
    }

    public function exportHistory(Request $request, Items $item, string $format)
    {
        $histories = $this->historyDistributionQuery($item, $request)->get();
        $filename = 'history-distribusi-' . $item->serial_number . '-' . now()->format('Ymd-His');

        if ($format === 'excel') {
            return $this->downloadHistoryExcel($item, $histories, $filename . '.xls');
        }

        if ($format === 'pdf') {
            return view('items.history_pdf', [
                'item' => $item,
                'histories' => $histories,
                'historyFilters' => $this->historyFilters($request),
            ]);
        }

        abort(404);
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

    private function downloadHistoryExcel(Items $item, $histories, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($item, $histories) {
            echo view('items.history_excel', [
                'item' => $item,
                'histories' => $histories,
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
        $kategoriOptions = Items::getKategoriOptions();
        $locations = Locations::where('type', 'warehouse')->get();
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
            'serial_number' => 'required|string|unique:items,serial_number,' . $item->id,
            'service_tag' => 'nullable|string|max:255',
            'processor' => 'nullable|string|max:255',
            'ram_gb' => 'nullable|integer|min:1',
            'storage_gb' => 'nullable|integer|min:1',
            'vga' => 'nullable|string|max:255',
            'os' => 'nullable|string|max:255',
            'tahun' => 'nullable|digits:4',
            'storage_location_id' => 'nullable|exists:locations,id',
            'status' => 'nullable|in:used,available,maintenance,retired',
            'condition_note' => 'nullable|string|max:255',

        ]);

        //TIDAK BOLEH UBAH STATUS JIKA USED
        if ($item->status=='used'){
            // paksa status tetap used
            $validated['status'] = 'used';
             // lokasi juga jangan berubah
            unset($validated['storage_location_id']);
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

        return redirect($this->redirectTarget($request, route('items.index')))->with('success', 'Data berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Int $id)
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


        return redirect($this->redirectTarget($request, route('items.index')))->with('success', 'Item berhasil dihapus !');
    }
}
