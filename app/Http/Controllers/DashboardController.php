<?php

namespace App\Http\Controllers;

use App\Models\Items;
use App\Models\Distribution;
use App\Models\Barang_masuk;
use App\Models\IssueReport;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard
     */
    public function index()
    {
        $user = auth()->user();

        if (! $user->isOperator()) {
            return redirect()->route('dashboard.client');
        }

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
            'vendor' => $statusCount('vendor'),
            'active_distributions' => Distribution::where('status', 'dipakai')->count(),
            'barang_masuk' => Barang_masuk::count(),
            'barang_masuk_bulan_ini' => Barang_masuk::whereBetween('tanggal_masuk', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString(),
            ])->count(),
            'issue_reports_open' => IssueReport::whereIn('status', [
                IssueReport::STATUS_OPEN,
                IssueReport::STATUS_IN_PROGRESS,
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
                'vendor' => $kategoriStatusCount($kategori, 'vendor'),
            ];
        });

        $assetList = Items::select('asset')
            ->whereNotNull('asset')
            ->where('asset', '!=', '')
            ->distinct()
            ->orderBy('asset')
            ->pluck('asset');

        $assetStatusCount = function (string $asset, string $status) use ($activeDistribution) {
            return Items::where('asset', $asset)
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

        $assetUsedCount = function (string $asset) use ($activeDistribution) {
            return Items::where('asset', $asset)
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

        $stokPerAsset = $assetList->map(function ($asset) use ($assetStatusCount, $assetUsedCount) {
            return [
                'asset' => $asset,
                'total' => Items::where('asset', $asset)->count(),
                'available' => $assetStatusCount($asset, 'available'),
                'used' => $assetUsedCount($asset),
                'maintenance' => $assetStatusCount($asset, 'maintenance'),
                'retired' => $assetStatusCount($asset, 'retired'),
                'vendor' => $assetStatusCount($asset, 'vendor'),
                'url' => route('items.index', ['asset' => [$asset]]),
            ];
        });

        $distribusiTerbaru = Distribution::with([
            'distributionItems.item',
            'location',
            'user',
        ])
        ->latest()
        ->take(5)
        ->get();

        $laporanKendalaTerbaru = IssueReport::with([
                'reporter',
                'item',
                'location',
            ])
            ->whereIn('status', [
                IssueReport::STATUS_OPEN,
                IssueReport::STATUS_IN_PROGRESS,
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
                'title' => 'Dibawa Vendor',
                'description' => 'Barang yang sedang berada di vendor untuk dicek atau diperbaiki.',
                'count' => $summary['vendor'],
                'url' => route('items.index', ['status' => 'vendor']),
                'color' => 'purple',
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
                    ->filter()
                    ->unique('id')
                    ->values();

                $locationUrl = $location
                    ? route('distribution.index', [
                        'status' => 'dipakai',
                        'gedung' => $location->gedung,
                        'ruangan' => $location->id,
                    ])
                    : route('distribution.index', ['status' => 'dipakai']);

                $row = [
                    'gedung' => $location->gedung ?? '-',
                    'ruangan' => $location->ruangan ?? '-',
                    'total' => $items->count(),
                    'url' => $locationUrl,
                ];

                foreach ($kategoriList as $kategori) {
                    $row[$kategori] = $items->where('kategori', $kategori)->count();
                }

                return $row;
            })
            ->sortByDesc('total')
            ->values();

        $distribusiAktifPerAsset = Distribution::with([
                'distributionItems.item',
            ])
            ->where('status', 'dipakai')
            ->get()
            ->flatMap(fn ($distribution) => $distribution->distributionItems)
            ->where('status', 'dipakai')
            ->map(fn ($distributionItem) => $distributionItem->item)
            ->filter(fn ($item) => $item && trim((string) ($item->asset ?? '')) !== '')
            ->unique('id')
            ->groupBy(fn ($item) => trim((string) $item->asset))
            ->map(function ($items, $asset) use ($kategoriList) {
                $row = [
                    'asset' => $asset,
                    'total' => $items->count(),
                    'url' => route('items.index', ['asset' => [$asset], 'status' => 'used']),
                ];

                foreach ($kategoriList as $kategori) {
                    $row[$kategori] = $items->where('kategori', $kategori)->count();
                }

                return $row;
            })
            ->sortByDesc('total')
            ->values();

        return view('dashboard', compact('summary', 'stokPerKategori','stokPerAsset','distribusiTerbaru','laporanKendalaTerbaru','perluPerhatian','distribusiPerLokasi','distribusiAktifPerAsset'));
    }

    public function client()
    {
        $user = auth()->user();

        if ($user->isOperator()) {
            return redirect()->route('dashboard');
        }

        $myDistributions = Distribution::with([
                'location',
                'distributionItems.item',
            ])
            ->where('user_id', $user->id)
            ->where('status', 'dipakai')
            ->latest()
            ->get();

        $myReports = IssueReport::with(['item', 'location'])
            ->where('reporter_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $myReportSummary = [
            'open' => IssueReport::where('reporter_id', $user->id)->where('status', IssueReport::STATUS_OPEN)->count(),
            'in_progress' => IssueReport::where('reporter_id', $user->id)->where('status', IssueReport::STATUS_IN_PROGRESS)->count(),
            'resolved' => IssueReport::where('reporter_id', $user->id)->where('status', IssueReport::STATUS_RESOLVED)->count(),
            'closed' => IssueReport::where('reporter_id', $user->id)->where('status', IssueReport::STATUS_CLOSED)->count(),
        ];

        return view('dashboard_client', compact('myDistributions', 'myReports', 'myReportSummary'));
    }
}
