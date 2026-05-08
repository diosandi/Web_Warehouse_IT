<?php

namespace App\Http\Controllers;
use App\Models\Locations;
use App\Models\Distribution;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class LocationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function searchLocations(Request $request)
    {
        $q = $request->q;

        $locations = Locations::where('gedung', 'like', "%$q%")
            ->orWhere('ruangan', 'like', "%$q%")
            ->limit(10)
            ->get();

        return response()->json(
            $locations->map(function ($loc) {
                return [
                    'id' => $loc->id,
                    'text' => $loc->gedung . ' - ' . $loc->ruangan
                ];
            })
        );
    }


    public function index(Request $request)
    {
        $query = Locations::query();
        $selectedGedung = array_values(array_filter((array) $request->input('gedung','')));
        $selectedRuangan = array_values(array_filter((array) $request->input('ruangan','')));

            if ($request->location_id) {
            // 🔥 kalau pilih dari suggestion → pakai ID saja
            $query->where('id', $request->location_id);
        }
            elseif ($request->filled('search')) {
                // 🔥 kalau manual ketik → pakai search
                $search = '%' . $request->search . '%';
                $query->where(function ($q) use ($search) {
                    $q->where('gedung', 'like', $search)
                    ->orWhere('ruangan', 'like', $search);
                });
        }

        //Filter by gedung
        if(!empty($selectedGedung)){
            $query->whereIn('gedung',$selectedGedung);
        }

        //Filter by ruangan
        if(!empty($selectedRuangan)){
            $query->whereIn('ruangan',$selectedRuangan);
        }

        $locations = $query->latest()->paginate(10)->appends($request->query());

         // Get distinct gedung list
        $gedungList = Locations::select('gedung')
            ->whereNotNull('gedung')
            ->where('gedung', '!=', '')
            ->distinct()
            ->orderBy('gedung')
            ->pluck('gedung')
            ->toArray();

        // Get distinct ruangan list
        $ruanganList = Locations::select('ruangan')
            ->whereNotNull('ruangan')
            ->where('ruangan', '!=', '')
            ->distinct()
            ->orderBy('ruangan')
            ->pluck('ruangan')
            ->toArray();

        // Get current filters for display
        $filters = [
            'gedung' => $selectedGedung,
            'ruangan' => $selectedRuangan,
            'search' => $request->search
        ];

        return view('locations.index',compact('locations','gedungList','ruanganList','selectedGedung','selectedRuangan','filters'));
        // return view('warehouse.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $typeOptions = Locations::gettypeOptions();
        return view('locations.create', compact('typeOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Locations $location)
    {
        $validated = $request->validate([
            'type'=> 'required|in:warehouse,distribution,maintenance',
            'gedung'=> 'required|max:255',
            'ruangan'=> ['nullable','string','max:255',
                         Rule::unique('locations')->where (function($query)use($request){
                            return $query->where('gedung',$request->gedung);
                         })->ignore($location->id)
            ]
        ]);

        Locations::create($validated);

        return redirect()->route('locations.index')->with('success','Lokasi berhasil dibuat !');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Locations $location)
    {
        $typeOptions = Locations::getTypeOptions();
        return view('locations.edit', ['location' => $location],['typeOptions' => $typeOptions]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Locations $location)
    {
        $validated = $request->validate([
            'type'=> 'required|in:warehouse,distribution,maintenance',
            'gedung'=> 'required|max:255',
            'ruangan'=> ['nullable','string','max:255',
                         Rule::unique('locations')->where (function($query)use($request){
                            return $query->where('gedung',$request->gedung);
                         })->ignore($location->id)
            ]
        ]);

        $location->update($validated);

        return redirect()->route('locations.index')->with('success','Lokasi berhasil diperbarui !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $location = Locations::findOrfail($id);
        // Cek lokasi dipakai atau tidak di distribusi
        $dipakai = Distribution::where('location_id', $id)->exists();

        if($dipakai) {
            return redirect()->back()
            ->with ('error', 'Lokasi tidak bisa dihapus karena sedang digunakan !');
        }
        $location->delete();
        return redirect()->route('locations.index')->with('success','Lokasi berhasil dihapus !');
    }
}
