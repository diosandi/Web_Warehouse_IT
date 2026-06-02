<?php

namespace App\Http\Controllers;

use App\Models\Items;
use App\Models\Distribution;
use App\Models\Barang_masuk;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard
     */
    public function index()
    {
        $activeDistribution = function ($query) {
            $query->where('status', 'dipakai')
                ->whereHas('distribution', function ($distribution) {
                    $distribution->where('status', 'dipakai');
                });
        };

        $statusCount = function (string $status) use ($activeDistribution) {
            return Items::where('status', $status)
                ->where(function ($query) use ($activeDistribution) {
                    $query->whereNotIn('kategori', ['Printer Kertas', 'Printer Barcode'])
                        ->orWhere(function ($printerQuery) use ($activeDistribution) {
                            $printerQuery->whereIn('kategori', ['Printer Kertas', 'Printer Barcode'])
                                ->whereDoesntHave('distributionItems', $activeDistribution);
                        });
                })
                ->count();
        };

        $summary =[
            'total_items' => Items::count(),
            'available' => $statusCount('available'),
            'used' => Items::where(function ($query) use ($activeDistribution) {
                $query->where(function ($printerQuery) use ($activeDistribution) {
                    $printerQuery->whereIn('kategori', ['Printer Kertas', 'Printer Barcode'])
                        ->whereHas('distributionItems', $activeDistribution);
                })->orWhere(function ($nonPrinterQuery) {
                    $nonPrinterQuery->whereNotIn('kategori', ['Printer Kertas', 'Printer Barcode'])
                        ->where('status', 'used');
                });
            })->count(),
            'maintenance' => $statusCount('maintenance'),
            'retired' => $statusCount('retired'),
            'active_distributions' => Distribution::where('status', 'dipakai')->count(),
            'barang_masuk' => Barang_masuk::count(),
            'barang_masuk_bulan_ini' => Barang_masuk::whereBetween('tanggal_masuk', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString(),
            ])->count(),
        ];

        $kategoriList = [
            'PC',
            'Monitor',
            'Printer Kertas',
            'Printer Barcode',
            'Scanner',
            'Lainnya',
        ];

        $kategoriStatusCount = function (string $kategori, string $status) use ($activeDistribution) {
            return Items::where('kategori', $kategori)
                ->where('status', $status)
                ->where(function ($query) use ($activeDistribution) {
                    $query->whereNotIn('kategori', ['Printer Kertas', 'Printer Barcode'])
                        ->orWhere(function ($printerQuery) use ($activeDistribution) {
                            $printerQuery->whereIn('kategori', ['Printer Kertas', 'Printer Barcode'])
                                ->whereDoesntHave('distributionItems', $activeDistribution);
                        });
                })
                ->count();
        };

        $kategoriUsedCount = function (string $kategori) use ($activeDistribution) {
            return Items::where('kategori', $kategori)
                ->where(function ($query) use ($activeDistribution) {
                    $query->where(function ($printerQuery) use ($activeDistribution) {
                        $printerQuery->whereIn('kategori', ['Printer Kertas', 'Printer Barcode'])
                            ->whereHas('distributionItems', $activeDistribution);
                    })->orWhere(function ($nonPrinterQuery) {
                        $nonPrinterQuery->whereNotIn('kategori', ['Printer Kertas', 'Printer Barcode'])
                            ->where('status', 'used');
                    });
                })
                ->count();
        };

        $stokPerKategori = collect($kategoriList)->map(function ($kategori) use ($kategoriStatusCount, $kategoriUsedCount) {
            return [
                'kategori' => $kategori,
                'total' => Items::where('kategori', $kategori)->count(),
                'available' => $kategoriStatusCount($kategori, 'available'),
                'used' => $kategoriUsedCount($kategori),
                'maintenance' => $kategoriStatusCount($kategori, 'maintenance'),
                'retired' => $kategoriStatusCount($kategori, 'retired'),
            ];
        });

        $distribusiTerbaru = Distribution::with([
            'distributionItems.item',
            'location'
        ])
        ->latest()
        ->take(5)
        ->get();

        $perluPerhatian = [
            [
                'title' => 'Barang Pemeliharaan',
                'description' => 'Barang yang sedang dalam status pemeliharaan.',
                'count' => $summary['maintenance'],
                'url' => route('items.index', ['status' => 'maintenance']),
                'color' => 'red',
            ],
            [
                'title' => 'Tanpa Detail Perangkat',
                'description' => 'Barang yang belum memiliki data detail perangkat.',
                'count' => Items::whereDoesntHave('device_detail')->count(),
                'url' => route('items.index', ['kelengkapan' => 'tanpa_detail']),
                'color' => 'yellow',
            ],
            [
                'title' => 'Tanpa Lokasi Penyimpanan',
                'description' => 'Barang tersedia yang belum memiliki lokasi penyimpanan.',
                'count' => Items::where('status', 'available')
                    ->whereNull('storage_location_id')
                    ->count(),
                'url' => route('items.index', ['status' => 'available']),
                'color' => 'blue',
            ],
            [
                'title' => 'Data Belum Lengkap',
                'description' => 'Barang dengan merk, tipe, serial number, atau kategori kosong.',
                'count' => Items::where(function ($query) {
                        $query->whereNull('merk')
                            ->orWhere('merk', '')
                            ->orWhereNull('type')
                            ->orWhere('type', '')
                            ->orWhereNull('serial_number')
                            ->orWhere('serial_number', '')
                            ->orWhereNull('kategori')
                            ->orWhere('kategori', '');
                    })
                    ->count(),
                'url' => route('items.index'),
                'color' => 'gray',
            ],
        ];

        $distribusiPerLokasi = Distribution::with([
                'location',
                'distributionItems.item',
            ])
            ->where('status', 'dipakai')
            ->get()
            ->groupBy('location_id')
            ->map(function ($distributions) use ($kategoriList) {
                $location = $distributions->first()->location;
                $items = $distributions
                    ->flatMap(fn ($distribution) => $distribution->distributionItems)
                    ->where('status', 'dipakai')
                    ->map(fn ($distributionItem) => $distributionItem->item)
                    ->filter();

                $row = [
                    'gedung' => $location->gedung ?? '-',
                    'ruangan' => $location->ruangan ?? '-',
                    'total' => $items->count(),
                ];

                foreach ($kategoriList as $kategori) {
                    $row[$kategori] = $items->where('kategori', $kategori)->count();
                }

                return $row;
            })
            ->sortByDesc('total')
            ->values();

        return view('dashboard', compact('summary', 'stokPerKategori','distribusiTerbaru','perluPerhatian','distribusiPerLokasi'));
    }
}
