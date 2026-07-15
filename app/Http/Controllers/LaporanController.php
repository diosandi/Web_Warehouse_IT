<?php

namespace App\Http\Controllers;

use App\Models\Barang_masuk;
use App\Models\Distribution;
use App\Models\Items;
use App\Support\DateFormatter;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->warehouseReportData($request);

        return view('laporan.index', $data);
    }

    public function export(Request $request, string $format)
    {
        $data = $this->warehouseReportData($request);
        $filename = 'laporan-warehouse-it-' . now()->format('Ymd-His');

        if ($format === 'excel') {
            return response()->streamDownload(function () use ($data) {
                echo view('laporan.warehouse_excel', $data)->render();
            }, $filename . '.xls', [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            ]);
        }

        if ($format === 'pdf') {
            return view('laporan.warehouse_pdf', $data);
        }

        abort(404);
    }

    private function warehouseReportData(Request $request): array
    {
        $kategoriOptions = Items::getKategoriOptions();
        $kategoriList = array_keys($kategoriOptions);
        $tanggalDari = $request->filled('tanggal_dari') ? $request->tanggal_dari : null;
        $tanggalSampai = $request->filled('tanggal_sampai') ? $request->tanggal_sampai : null;
        $selectedKategori = $this->filterValues($request, 'kategori');
        $selectedMerk = $this->filterValues($request, 'merk');
        $selectedAsset = $this->filterValues($request, 'asset');
        $periodeLabel = $this->periodeLabel($tanggalDari, $tanggalSampai);
        $filterLabel = $this->filterLabel($selectedKategori, $selectedMerk, $selectedAsset);
        $hasBarangMasukFilters = ! empty($selectedKategori) || ! empty($selectedMerk) || ! empty($selectedAsset);

        $barangMasukItemFilter = function ($query) use ($selectedKategori, $selectedMerk, $selectedAsset) {
            $this->applyBarangMasukItemFilters($query, $selectedKategori, $selectedMerk, $selectedAsset);
        };

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

        $usedCount = function (?string $kategori = null) use ($activeDistribution) {
            return Items::when($kategori, fn ($query) => $query->where('kategori', $kategori))
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

        $summary = [
            'total_items' => Items::count(),
            'available' => $statusCount('available'),
            'used' => $usedCount(),
            'maintenance' => $statusCount('maintenance'),
            'retired' => $statusCount('retired'),
            'vendor' => $statusCount('vendor'),
            'active_distributions' => Distribution::where('status', 'dipakai')->count(),
            'barang_masuk' => Barang_masuk::count(),
        ];

        $stokPerKategori = collect($kategoriList)->map(function ($kategori) use ($activeDistribution, $usedCount) {
            $kategoriStatusCount = function (string $status) use ($kategori, $activeDistribution) {
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

            return [
                'kategori' => $kategori,
                'total' => Items::where('kategori', $kategori)->count(),
                'available' => $kategoriStatusCount('available'),
                'used' => $usedCount($kategori),
                'maintenance' => $kategoriStatusCount('maintenance'),
                'retired' => $kategoriStatusCount('retired'),
                'vendor' => $kategoriStatusCount('vendor'),
            ];
        });

        $distribusiPerLokasi = Distribution::with(['location', 'distributionItems.item'])
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

        $barangMasuk = Barang_masuk::with(['items' => $barangMasukItemFilter])
            ->when($tanggalDari, fn ($query) => $query->whereDate('tanggal_masuk', '>=', $tanggalDari))
            ->when($tanggalSampai, fn ($query) => $query->whereDate('tanggal_masuk', '<=', $tanggalSampai))
            ->when($hasBarangMasukFilters, fn ($query) => $query->whereHas('items', $barangMasukItemFilter))
            ->latest('tanggal_masuk')
            ->get();

        $barangMasukPerKategori = collect($kategoriList)->map(function ($kategori) use ($barangMasuk) {
            $items = $barangMasuk->flatMap->items->where('kategori', $kategori);

            return [
                'kategori' => $kategori,
                'jumlah' => $items->count(),
            ];
        });

        $barangMasukDetail = $barangMasuk->map(function ($barang) {
            $firstItem = $barang->items->first();

            return [
                'tanggal_masuk' => $barang->tanggal_masuk,
                'asset' => $barang->supplier ?? '-',
                'supplier' => $barang->supplier ?? '-',
                'po_number' => $barang->po_number ?? '-',
                'kategori' => $firstItem->kategori ?? '-',
                'merk' => $firstItem->merk ?? '-',
                'type' => $firstItem->type ?? '-',
                'jumlah' => $barang->items->count(),
                'keterangan' => $barang->keterangan ?? '-',
            ];
        });

        $totalBarangMasukPeriode = $barangMasuk->sum(fn ($barang) => $barang->items->count());

        $maintenanceItems = Items::with('storageLocation')
            ->where('status', 'maintenance')
            ->orderBy('kategori')
            ->orderBy('merk')
            ->get();

        $itemsTanpaDetail = Items::whereDoesntHave('device_detail')
            ->orderBy('kategori')
            ->orderBy('merk')
            ->get();

        $itemsTanpaLokasi = Items::with('storageLocation')
            ->where('status', 'available')
            ->whereNull('storage_location_id')
            ->orderBy('kategori')
            ->orderBy('merk')
            ->get();

        $itemsBelumLengkap = Items::where(function ($query) {
                $query->whereNull('merk')
                    ->orWhere('merk', '')
                    ->orWhereNull('type')
                    ->orWhere('type', '')
                    ->orWhereNull('serial_number')
                    ->orWhere('serial_number', '')
                    ->orWhereNull('kategori')
                    ->orWhere('kategori', '');
            })
            ->orderBy('kategori')
            ->orderBy('merk')
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
                'count' => $itemsTanpaDetail->count(),
                'url' => route('items.index'),
                'color' => 'yellow',
            ],
            [
                'title' => 'Tanpa Lokasi Penyimpanan',
                'description' => 'Barang tersedia yang belum memiliki lokasi penyimpanan.',
                'count' => $itemsTanpaLokasi->count(),
                'url' => route('items.index', ['status' => 'available']),
                'color' => 'blue',
            ],
            [
                'title' => 'Data Belum Lengkap',
                'description' => 'Barang dengan merk, tipe, serial number, atau kategori kosong.',
                'count' => $itemsBelumLengkap->count(),
                'url' => route('items.index'),
                'color' => 'gray',
            ],
        ];

        $assetList = $this->assetList();
        $merkList = $this->merkList($selectedAsset);
        $filters = [
            'kategori' => $selectedKategori,
            'merk' => $selectedMerk,
            'asset' => $selectedAsset,
        ];

        return compact(
            'summary',
            'stokPerKategori',
            'distribusiPerLokasi',
            'barangMasukPerKategori',
            'barangMasukDetail',
            'totalBarangMasukPeriode',
            'maintenanceItems',
            'itemsTanpaDetail',
            'itemsTanpaLokasi',
            'itemsBelumLengkap',
            'perluPerhatian',
            'tanggalDari',
            'tanggalSampai',
            'periodeLabel',
            'filterLabel',
            'kategoriOptions',
            'merkList',
            'assetList',
            'filters'
        );
    }

    private function periodeLabel(?string $tanggalDari, ?string $tanggalSampai): string
    {
        return DateFormatter::dateRange($tanggalDari, $tanggalSampai);
    }

    private function filterValues(Request $request, string $key): array
    {
        return collect((array) $request->input($key, []))
            ->map(fn ($value) => trim((string) $value))
            ->filter(fn ($value) => $value !== '')
            ->values()
            ->all();
    }

    private function applyBarangMasukItemFilters($query, array $selectedKategori, array $selectedMerk, array $selectedAsset): void
    {
        if (! empty($selectedAsset)) {
            $query->where(function ($assetQuery) use ($selectedAsset) {
                $assetQuery->whereIn('asset', $selectedAsset)
                    ->orWhereHas('barang_masuk', fn ($barangMasuk) => $barangMasuk->whereIn('supplier', $selectedAsset));
            });
        }

        if (! empty($selectedKategori)) {
            $query->whereIn('kategori', $selectedKategori);
        }

        if (! empty($selectedMerk)) {
            $query->whereIn('merk', $selectedMerk);
        }
    }

    private function assetList(): array
    {
        $itemAssets = Items::whereNotNull('asset')
            ->where('asset', '!=', '')
            ->pluck('asset');

        $barangMasukAssets = Barang_masuk::whereNotNull('supplier')
            ->where('supplier', '!=', '')
            ->pluck('supplier');

        return $itemAssets
            ->merge($barangMasukAssets)
            ->map(fn ($asset) => trim((string) $asset))
            ->filter(fn ($asset) => $asset !== '')
            ->unique()
            ->sortBy(fn ($asset) => strtolower($asset))
            ->values()
            ->all();
    }

    private function merkList(array $selectedAsset): array
    {
        return Items::query()
            ->when(! empty($selectedAsset), function ($query) use ($selectedAsset) {
                $query->where(function ($assetQuery) use ($selectedAsset) {
                    $assetQuery->whereIn('asset', $selectedAsset)
                        ->orWhereHas('barang_masuk', fn ($barangMasuk) => $barangMasuk->whereIn('supplier', $selectedAsset));
                });
            })
            ->whereNotNull('merk')
            ->where('merk', '!=', '')
            ->distinct()
            ->orderBy('merk')
            ->pluck('merk')
            ->toArray();
    }

    private function filterLabel(array $selectedKategori, array $selectedMerk, array $selectedAsset): string
    {
        $parts = [];

        if (! empty($selectedAsset)) {
            $parts[] = 'Asset: ' . implode(', ', $selectedAsset);
        }

        if (! empty($selectedKategori)) {
            $parts[] = 'Kategori: ' . implode(', ', $selectedKategori);
        }

        if (! empty($selectedMerk)) {
            $parts[] = 'Merk: ' . implode(', ', $selectedMerk);
        }

        return ! empty($parts) ? implode(' | ', $parts) : 'Semua asset, kategori, dan merk';
    }
}
