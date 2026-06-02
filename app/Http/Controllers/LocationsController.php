<?php

namespace App\Http\Controllers;
use App\Models\Locations;
use App\Models\Distribution;
use App\Http\Controllers\Concerns\ResolvesRedirects;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class LocationsController extends Controller
{
    use ResolvesRedirects;
    /**
     * Display a listing of the resource.
     */
    public function searchLocations(Request $request)
    {
        $q = $request->q;

        $locations = Locations::where('gedung', 'like', "%$q%")
            ->orWhere('ruangan', 'like', "%$q%")
            ->orWhere('type', 'like', "%$q%")
            ->limit(10)
            ->get();

        return response()->json(
            $locations->map(function ($loc) {
                return [
                    'id' => $loc->id,
                    'text' => $loc->gedung . ' - ' . $loc->ruangan . ' - ' . $loc->type
                ];
            })
        );
    }


    public function index(Request $request)
    {
        $query = Locations::query();
        $selectedGedung = array_values(array_filter((array) $request->input('gedung','')));
        $selectedRuangan = array_values(array_filter((array) $request->input('ruangan','')));
        $selectedType = array_values(array_filter((array) $request->input('type', '')));

            if ($request->location_id) {
            // 🔥 kalau pilih dari suggestion → pakai ID saja
            $query->where('id', $request->location_id);
        }
            elseif ($request->filled('search')) {
                // 🔥 kalau manual ketik → pakai search
                $search = '%' . $request->search . '%';
                $query->where(function ($q) use ($search) {
                    $q->where('gedung', 'like', $search)
                    ->orWhere('ruangan', 'like', $search)
                    ->orWhere('type','like', $search);
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

        //Filter by type
        if(!empty($selectedType)){
            $query->whereIn('type',$selectedType);
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
            'type' => $selectedType,
            'search' => $request->search
        ];

        return view('locations.index',compact('locations','gedungList','ruanganList','selectedGedung','selectedRuangan','selectedType','filters'));
        // return view('warehouse.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $typeOptions = Locations::gettypeOptions();
        $redirect = $this->redirectTarget($request, route('locations.index'));
        return view('locations.create', compact('typeOptions', 'redirect'));
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

        return redirect($this->redirectTarget($request, route('locations.index')))->with('success','Lokasi berhasil dibuat !');
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
    public function edit(Request $request, Locations $location)
    {
        $typeOptions = Locations::getTypeOptions();
        $redirect = $this->redirectTarget($request, route('locations.index'));
        return view('locations.edit', compact('location', 'typeOptions', 'redirect'));
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

        return redirect($this->redirectTarget($request, route('locations.index')))->with('success','Lokasi berhasil diperbarui !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $location = Locations::findOrfail($id);
        // Cek lokasi dipakai atau tidak di distribusi
        $dipakai = Distribution::where('location_id', $id)->exists();

        if($dipakai) {
            return redirect()->back()
            ->with ('error', 'Lokasi tidak bisa dihapus karena sedang digunakan !');
        }
        $location->delete();
        return redirect($this->redirectTarget($request, route('locations.index')))->with('success','Lokasi berhasil dihapus !');
    }
}
