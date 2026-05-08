<?php

namespace App\Http\Controllers;

use App\Models\Device_details;
use App\Models\Items;
use Illuminate\Http\Request;

class Device_detailsController extends Controller
{
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
        $query = Device_details::with('item');

                if ($request->filled('search')) {
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

                //FILTER MERK
                if ($request->merk) {
                    $query->whereHas('item', function ($q) use ($request) {
                        $q->where('merk', $request->merk);
                    });
                }

                // 🖥️ FILTER OS
                if ($request->os) {
                    $query->where('os_version', 'like', '%' . $request->os . '%');
                }

                $deviceDetails = $query->latest()->paginate(10);
                return view('device_details.index', compact('deviceDetails', 'merks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = Items::all();
        return view('device_details.create', compact('items'));
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

        return redirect()->route('device_details.index')->with('success', 'device detail berhasil ditambahkan!');
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
        return view('device_details.edit', compact('device_detail', 'items'));
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

        return redirect()->route('device_details.index')->with('success', 'Device detail berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Device_details $device_detail)
    {
        $device_detail->delete();
        return redirect()->route('device_details.index')->with('success', 'Device detail berhasil dihapus!');
    }
}
