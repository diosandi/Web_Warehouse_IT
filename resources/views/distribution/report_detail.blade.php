@extends('layouts.app')

@section('content')
@php
    $selectedAssets = array_values(array_filter((array) request('asset', [])));
    $filterQuery = request()->except('kategori_laporan', 'asset', 'page');
    $categoryOptions = [
        'pc' => 'PC',
        'monitor' => 'Monitor',
        'printer_kertas' => 'Printer Kertas',
        'printer_barcode' => 'Printer Barcode',
        'scanner' => 'Scanner',
        'lainnya' => 'Lainnya',
    ];

    $selectedCategories = request()->has('kategori_laporan')
        ? (array) request('kategori_laporan')
        : array_keys($categoryOptions);

    $showCategory = fn ($category) => in_array($category, $selectedCategories, true);
@endphp

<br>
<div class="distribution-page mx-auto w-full px-3 py-8 sm:px-4 lg:px-6 lg:py-12">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Laporan Detail Distribusi</h1>
            <p class="text-sm text-gray-600 mt-1">Satu baris per distribusi, berisi perangkat dan detail yang dipilih.</p>
        </div>

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <a href="{{ route('distribution.report_detail.export', array_merge(['format' => 'excel'], request()->query())) }}"
               class="btn btn-success btn-sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m4 5H5a2 2 0 01-2-2V6a2 2 0 012-2h8l6 6v8a2 2 0 01-2 2z"></path>
                </svg>
                Export Excel
            </a>
            <a href="{{ route('distribution.report_detail.export', array_merge(['format' => 'pdf'], request()->query())) }}"
               target="_blank"
               class="btn btn-danger btn-sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m4 5H5a2 2 0 01-2-2V6a2 2 0 012-2h8l6 6v8a2 2 0 01-2 2z"></path>
                </svg>
                Export PDF
            </a>
            <a href="{{ route('distribution.index', $filterQuery) }}"
               class="btn btn-secondary btn-sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <div class="mb-6 rounded-xl bg-white p-4 shadow-lg md:p-6">
        <form method="GET" action="{{ route('distribution.report_detail') }}" class="space-y-4">
            @foreach($filterQuery as $name => $value)
                @if(is_array($value))
                    @foreach($value as $item)
                        <input type="hidden" name="{{ $name }}[]" value="{{ $item }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                @endif
            @endforeach

            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Kategori Yang Ditampilkan</h2>
                    <p class="mt-1 text-sm text-gray-500">Pilih kategori yang ingin muncul di laporan.</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="button" id="selectAllCategories"
                        class="btn btn-light btn-sm">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Pilih Semua
                    </button>
                    <button type="submit"
                        class="btn btn-success btn-sm">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707L14 14v4l-4 2v-6L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Terapkan
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-6">
                @foreach($categoryOptions as $value => $label)
                    <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-semibold text-gray-700 transition hover:border-green-400 hover:bg-green-50">
                        <input type="checkbox"
                            name="kategori_laporan[]"
                            value="{{ $value }}"
                            class="report-category rounded border-gray-300 text-green-600 focus:ring-green-500"
                            {{ $showCategory($value) ? 'checked' : '' }}>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>

            @if(!empty($assetList))
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-3">Asset Yang Ditampilkan</h2>

                    <div class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-6">
                        @foreach($assetList as $asset)
                            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-semibold text-gray-700 transition hover:border-green-400 hover:bg-green-50">
                                <input type="checkbox"
                                    name="asset[]"
                                    value="{{ $asset }}"
                                    class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                                    {{ in_array($asset, $selectedAssets, true) ? 'checked' : '' }}>
                                <span>{{ $asset }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-4 md:px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <p class="text-sm text-gray-600">
                <span class="font-semibold text-gray-800">{{ $reports->total() }}</span> data laporan ditemukan
            </p>
            <p class="text-xs text-gray-500">Kolom mengikuti kategori yang dipilih.</p>
        </div>

        <div class="distribution-table-wrap overflow-x-auto">
            <table class="distribution-table w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-green-600 to-green-700">
                    <tr>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">No</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Nama User</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Divisi</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Gedung</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Ruangan</th>

                        @if($showCategory('pc'))
                            <th class="ppx-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">PC</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">SN PC / Asset</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Service Tag</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">PC Name</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">User Account</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">IP</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">MAC LAN</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Processor</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">RAM</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Storage</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">VGA</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">OS</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Office</th>
                        @endif

                        @if($showCategory('monitor'))
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Monitor</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">SN Monitor / Asset</th>
                        @endif

                        @if($showCategory('printer_kertas'))
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Printer Kertas</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">SN Printer / Asset</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Detail Printer</th>
                        @endif

                        @if($showCategory('printer_barcode'))
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Printer Barcode</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">SN Printer Barcode / Asset</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Detail Barcode</th>
                        @endif

                        @if($showCategory('scanner'))
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Scanner</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">SN Scanner / Asset</th>
                        @endif

                        @if($showCategory('lainnya'))
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Lainnya</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">SN Lainnya / Asset</th>
                        @endif

                        <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Tanggal Distribusi</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse($reports as $index => $distribution)
                        @php
                            $visibleItems = ($distribution->status === 'dikembalikan'
                                    ? $distribution->distributionItems
                                    : $distribution->distributionItems->where('status', 'dipakai'))
                                ->map(fn ($distributionItem) => $distributionItem->item)
                                ->filter()
                                ->filter(function ($item) use ($selectedAssets) {
                                    return empty($selectedAssets) || in_array((string) $item->asset, $selectedAssets, true);
                                });

                            $pc = $visibleItems->firstWhere('kategori', 'PC');
                            $pcDetail = $pc?->device_detail;

                            $monitors = $visibleItems->where('kategori', 'Monitor');
                            $printerKertas = $visibleItems->where('kategori', 'Printer Kertas');
                            $printerBarcode = $visibleItems->where('kategori', 'Printer Barcode');
                            $scanners = $visibleItems->where('kategori', 'Scanner');
                            $lainnya = $visibleItems->where('kategori', 'Lainnya');

                            $formatItem = function ($items) {
                                return $items->map(function ($item) {
                                    return trim(($item->merk ?? '-') . ' / ' . ($item->type ?? '-'));
                                })->filter()->implode(', ');
                            };

                            $formatSnWithAsset = function ($items) {
                                return $items->map(function ($item) {
                                    return e($item->serial_number ?: '-') .
                                        '<br><span class="text-xs font-semibold text-gray-500">Asset: ' .
                                        e($item->asset ?: '-') .
                                        '</span>';
                                })->filter()->implode('<hr class="my-2 border-gray-200">');
                            };

                            $formatSingleSnWithAsset = fn ($item) => $item ? $formatSnWithAsset(collect([$item])) : '-';

                            $formatPrinterDetail = function ($items) {
                                return $items->map(function ($item) {
                                    $detail = $item?->device_detail;
                                    $lines = [
                                        'Koneksi: ' . ($detail?->connection_type ?? '-'),
                                        'IP: ' . ($detail?->ip_address ?? '-'),
                                        'Shared: ' . ($detail?->shared_name ?? '-'),
                                    ];

                                    return implode('<br>', $lines);
                                })->implode('<hr class="my-2 border-gray-200">');
                            };
                        @endphp

                        <tr class="hover:bg-gray-50 align-top">
                            <td class="px-3 py-3">{{ $reports->firstItem() + $index }}</td>
                            <td class="px-3 py-3 uppercase font-semibold text-gray-900">{{ $distribution->nama_user ?? '-' }}</td>
                            <td class="px-3 py-3 uppercase">{{ $distribution->divisi ?? '-' }}</td>
                            <td class="px-3 py-3 uppercase">{{ $distribution->location->gedung ?? '-' }}</td>
                            <td class="px-3 py-3 uppercase">{{ $distribution->location->ruangan ?? '-' }}</td>

                            @if($showCategory('pc'))
                                <td class="px-3 py-3 uppercase">{{ $pc ? trim(($pc->merk ?? '-') . ' / ' . ($pc->type ?? '-')) : '-' }}</td>
                                <td class="px-3 py-3 uppercase leading-5">{!! $formatSingleSnWithAsset($pc) !!}</td>
                                <td class="px-3 py-3 uppercase">{{ $pc->service_tag ?? '-' }}</td>
                                <td class="px-3 py-3 uppercase">{{ $pcDetail->pc_name ?? '-' }}</td>
                                <td class="px-3 py-3 uppercase">{{ $pcDetail->user_account ?? '-' }}</td>
                                <td class="px-3 py-3 uppercase">{{ $pcDetail->ip_address ?? '-' }}</td>
                                <td class="px-3 py-3 uppercase">{{ $pcDetail->mac_lan ?? '-' }}</td>
                                <td class="px-3 py-3 uppercase">{{ $pc->processor ?? '-' }}</td>
                                <td class="px-3 py-3 uppercase">{{ $pc?->ram_gb ? $pc->ram_gb . ' GB' : '-' }}</td>
                                <td class="px-3 py-3 uppercase">{{ $pc?->storage_gb ? $pc->storage_gb . ' GB' : '-' }}</td>
                                <td class="px-3 py-3 uppercase">{{ $pc->vga ?? '-' }}</td>
                                <td class="px-3 py-3 uppercase">{{ $pc->os ?? '-' }}</td>
                                <td class="px-3 py-3 uppercase">{{ $pcDetail->office_version ?? '-' }}</td>
                            @endif

                            @if($showCategory('monitor'))
                                <td class="px-3 py-3 uppercase">{{ $formatItem($monitors) ?: '-' }}</td>
                                <td class="px-3 py-3 uppercase text-xs leading-5">{!! $monitors->count() ? $formatSnWithAsset($monitors) : '-' !!}</td>
                            @endif

                            @if($showCategory('printer_kertas'))
                                <td class="px-3 py-3 uppercase">{{ $formatItem($printerKertas) ?: '-' }}</td>
                                <td class="px-3 py-3 uppercase text-xs leading-5">{!! $printerKertas->count() ? $formatSnWithAsset($printerKertas) : '-' !!}</td>
                                <td class="px-3 py-3 uppercase text-xs leading-5">{!! $printerKertas->count() ? $formatPrinterDetail($printerKertas) : '-' !!}</td>
                            @endif

                            @if($showCategory('printer_barcode'))
                                <td class="px-3 py-3 uppercase">{{ $formatItem($printerBarcode) ?: '-' }}</td>
                                <td class="px-3 py-3 uppercase text-xs leading-5">{!! $printerBarcode->count() ? $formatSnWithAsset($printerBarcode) : '-' !!}</td>
                                <td class="px-3 py-3 uppercase text-xs leading-5">{!! $printerBarcode->count() ? $formatPrinterDetail($printerBarcode) : '-' !!}</td>
                            @endif

                            @if($showCategory('scanner'))
                                <td class="px-3 py-3 uppercase">{{ $formatItem($scanners) ?: '-' }}</td>
                                <td class="px-3 py-3 uppercase text-xs leading-5">{!! $scanners->count() ? $formatSnWithAsset($scanners) : '-' !!}</td>
                            @endif

                            @if($showCategory('lainnya'))
                                <td class="px-3 py-3 uppercase">{{ $formatItem($lainnya) ?: '-' }}</td>
                                <td class="px-3 py-3 uppercase text-xs leading-5">{!! $lainnya->count() ? $formatSnWithAsset($lainnya) : '-' !!}</td>
                            @endif

                            <td class="px-3 py-3">{{ \App\Support\DateFormatter::date($distribution->tanggal_distribusi) }}</td>
                            <td class="px-3 py-3">
                                @if($distribution->status === 'dipakai')
                                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">Dipakai</span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">Dikembalikan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="40" class="px-3 py-10 text-center text-gray-500">
                                Tidak ada data laporan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-4 border-t border-gray-200">
            {{ $reports->links() }}
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAllButton = document.getElementById('selectAllCategories');
    const checkboxes = document.querySelectorAll('.report-category');

    if (!selectAllButton) {
        return;
    }

    selectAllButton.addEventListener('click', function () {
        checkboxes.forEach(function (checkbox) {
            checkbox.checked = true;
        });
    });
});
</script>
@endsection
