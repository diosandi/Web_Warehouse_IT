<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRedirects;
use App\Models\Device_details;
use App\Models\Items;
use App\Models\Locations;
use Illuminate\Http\Request;

class Device_detailsController extends Controller
{
    use ResolvesRedirects;
        /**
     * AJAX search SN untuk Select2
     */
    public function searchItems(Request $request)
    {
        $q = $request->get('q');
        $items = \App\Models\Items::query()
            ->where('serial_number', 'like', "%{$q}%")
            ->orWhere('merk', 'like', "%{$q}%")
            ->orWhere('kategori', 'like', "%{$q}%")
            ->limit(20)
            ->get(['id', 'serial_number', 'merk', 'kategori']);
        return response()->json($items);
    }

    public function searchDeviceDetails(Request $request)
    {
        $q = $request->get('q');
        $search = "%{$q}%";
        $results = Device_details::with('item')
            ->where('ip_address', 'like', $search)
            ->orWhere('pc_name', 'like', $search)
            ->orWhere('mac_lan', 'like', $search)
            ->orWhere('mac_wifi', 'like', $search)
            ->orWhere('os_version', 'like', $search)
            ->orWhereHas('item', function ($itemQuery) use ($search) {
                $itemQuery->where('serial_number', 'like', $search)
                          ->orWhere('merk', 'like', $search)
                          ->orWhere('kategori', 'like', $search);
            })
            ->limit(10)
            ->get();

        $suggestions = $results->map(function($d) {
            $item = $d->item;
            $itemText = $item ? ($item->serial_number . ' - ' . $item->merk . ' - ' . $item->kategori) : '';
            return [
                'id' => $d->id,
                'serial_number' => $item ? $item->serial_number : '',
                'merk' => $item ? $item->merk : '',
                'kategori' => $item ? $item->kategori : '',
                'ip_address' => $d->ip_address,
                'text' => trim($itemText . ' | IP: ' . $d->ip_address . ' | MAC: ' . $d->mac_lan)
            ];
        });

        return response()->json($suggestions);
    }

    public function index(Request $request)
    {
        $query = Device_details::with([
            'item.storageLocation',
            'item.distributionItems.distribution.location',
        ]);

                if ($request->filled('item_id')) {
                    $query->whereKey($request->item_id);
                } elseif ($request->filled('search')) {
                    $search = '%' . $request->search . '%';
                    $query->where(function ($q) use ($search) {
                        $q->where('ip_address', 'like', $search)
                        ->orWhere('pc_name', 'like', $search)
                        ->orWhere('mac_lan', 'like', $search)
                        ->orWhere('mac_wifi', 'like', $search)
                        ->orWhere('os_version', 'like', $search)
                        ->orWhereHas('item', function ($itemQuery) use ($search) {
                            $itemQuery->where('serial_number', 'like', $search)
                                        ->orWhere('merk', 'like', $search);
                        });
                    });
                }

                $merks = Items::select('merk')
                ->whereNotNull('merk')
                ->distinct()
                ->pluck('merk');

                $gedungs = Locations::select('gedung')
                ->whereNotNull('gedung')
                ->distinct()
                ->orderBy('gedung')
                ->pluck('gedung');

                $locationsByGedung = Locations::select('gedung', 'ruangan')
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

                $connectionTypes = ['LAN', 'USB', 'WIFI', 'HDMI', 'VGA', 'DP'];
                $selectedConnectionTypes = array_values(array_filter(
                    (array) $request->input('connection_type', []),
                    fn ($connectionType) => in_array($connectionType, $connectionTypes, true)
                ));

                //FILTER MERK
                if ($request->merk) {
                    $query->whereHas('item', function ($q) use ($request) {
                        $q->where('merk', $request->merk);
                    });
                }

                //FILTER KONEKSI
                if (! empty($selectedConnectionTypes)) {
                    $query->where(function ($q) use ($selectedConnectionTypes) {
                        foreach ($selectedConnectionTypes as $connectionType) {
                            $q->orWhere(function ($typeQuery) use ($connectionType) {
                                $typeQuery->where('connection_type', $connectionType)
                                    ->orWhere('connection_type', 'like', $connectionType . ',%')
                                    ->orWhere('connection_type', 'like', $connectionType . ', %')
                                    ->orWhere('connection_type', 'like', '%,' . $connectionType)
                                    ->orWhere('connection_type', 'like', '%, ' . $connectionType)
                                    ->orWhere('connection_type', 'like', '%,' . $connectionType . ',%')
                                    ->orWhere('connection_type', 'like', '%, ' . $connectionType . ', %');
                            });
                        }
                    });
                }

                // 🖥️ FILTER OS
                if ($request->os) {
                    $query->where('os_version', 'like', '%' . $request->os . '%');
                }

                if ($request->filled('gedung') || $request->filled('ruangan')) {
                    $query->where(function ($q) use ($selectedGedung, $selectedRuangan) {
                        $q->whereHas('item.storageLocation', function ($storageLocationQuery) use ($selectedGedung, $selectedRuangan) {
                            if ($selectedGedung) {
                                $storageLocationQuery->where('gedung', 'like', '%' . $selectedGedung . '%');
                            }

                            if ($selectedRuangan) {
                                $storageLocationQuery->where('ruangan', 'like', '%' . $selectedRuangan . '%');
                            }
                        })
                        ->orWhereHas('item.distributionItems.distribution.location', function ($distributionLocationQuery) use ($selectedGedung, $selectedRuangan) {
                            if ($selectedGedung) {
                                $distributionLocationQuery->where('gedung', 'like', '%' . $selectedGedung . '%');
                            }

                            if ($selectedRuangan) {
                                $distributionLocationQuery->where('ruangan', 'like', '%' . $selectedRuangan . '%');
                            }
                        });
                    });
                }

                $deviceDetails = $query->latest()->paginate(50)->withQueryString();
                return view('device_details.index', compact('deviceDetails', 'merks', 'gedungs', 'locationsByGedung', 'selectedGedung', 'selectedRuangan', 'connectionTypes', 'selectedConnectionTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $item = Items::findOrFail($request->item_id);
        $items = Items::all();
        $redirect = $this->redirectTarget($request, route('items.show', $item));
        return view('device_details.create', compact('items','item', 'redirect'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => [
                'required',
                'exists:items,id',
                // custom rule: tidak boleh duplicate SN di device_details
                function($attribute, $value, $fail) {
                    if (\App\Models\Device_details::where('item_id', $value)->exists()) {
                        $fail('Serial Number ini sudah terdaftar di Device Details.');
                    }
                }
            ],
            'pc_name' => 'nullable|string|max:255',
            'user_account' => 'nullable|string|max:255',
            'ip_address' => 'nullable|string|max:255',
            'mac_lan' => 'nullable|string|max:255',
            'mac_wifi' => 'nullable|string|max:255',
            'connection_type' => 'nullable|string|max:255',
            'port' => 'nullable|string|max:255',
            'shared_name' => 'nullable|string|max:255',
            'os_version' => 'nullable|string|max:255',
            'build' => 'nullable|string|max:255',
            'office_version' => 'nullable|string|max:255',
            'office_key' => 'nullable|string|max:255',
            'antivirus' => 'nullable|string|max:255',
            'catatan' => 'nullable|string|max:255'
        ]);

        Device_details::create($validated);
        return redirect($this->redirectTarget($request, route('items.index')))->with('success', 'Data berhasil diperbarui!');
        // return redirect($request->redirect_to)->with('success', 'Device detail berhasil ditambahkan');

        // return redirect()->route('device_details.index')->with('success', 'device detail berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Device_details $device_detail)
    {
        return view('device_details.show', ['device_detail' => $device_detail]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Device_details $device_detail)
    {
        $items = Items::all();
        $redirect = $this->redirectTarget(request(), route('items.show', $device_detail->item_id));
        return view('device_details.edit', compact('device_detail', 'items', 'redirect'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Device_details $device_detail)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'pc_name'=> 'nullable|string|max:255',
            'user_account'=> 'nullable|string|max:255',
            'ip_address' => 'nullable|string|max:255',
            'mac_lan' => 'nullable|string|max:255',
            'mac_wifi' => 'nullable|string|max:255',
            'connection_type' => 'nullable|string|max:255',
            'port' => 'nullable|string|max:255',
            'shared_name' => 'nullable|string|max:255',
            'os_version' => 'nullable|string|max:255',
            'build' => 'nullable|string|max:255',
            'office_version' => 'nullable|string|max:255',
            'office_key' => 'nullable|string|max:255',
            'antivirus' => 'nullable|string|max:255',
            'catatan' => 'nullable|string|max:255'
        ]);

        $device_detail->update($validated);
        return redirect($this->redirectTarget($request, route('items.index')))->with('success', 'Data berhasil diperbarui!');
        // return redirect($request->redirect)->with('success', 'Berhasil update');
        // return redirect($request->redirect_to)->with('success', 'Device detail berhasil diupdate');
        // return redirect()->route('device_details.index')->with('success', 'Device detail berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Device_details $device_detail)
    {
        $device_detail->delete();
        return redirect($this->redirectTarget(request(), route('device_details.index')))->with('success', 'Device detail berhasil dihapus!');
    }
}
