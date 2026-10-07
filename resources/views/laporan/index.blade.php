@extends('layouts.app')

@section('content')
@php
    $selectedKategori = $filters['kategori'] ?? [];
    $selectedMerk = $filters['merk'] ?? [];
    $selectedAsset = $filters['asset'] ?? [];
@endphp
<br>
<div class="container mx-auto px-4 py-12">
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100">Ekspor Laporan Warehouse IT</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Buat file laporan keseluruhan warehouse IT dalam format Excel atau PDF.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <section class="xl:col-span-2 rounded-xl bg-white dark:bg-gray-700 p-6 shadow-lg">
            <div class="mb-5">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Periode Laporan</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Filter ini dipakai untuk bagian barang masuk. Ringkasan stok dan distribusi aktif tetap menampilkan kondisi saat ini.</p>
            </div>

            <form method="GET" action="{{ route('laporan.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Tanggal Dari</label>
                    <input type="date" name="tanggal_dari" value="{{ $tanggalDari }}" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm uppercase focus:border-transparent focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Tanggal Sampai</label>
                    <input type="date" name="tanggal_sampai" value="{{ $tanggalSampai }}" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm uppercase focus:border-transparent focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Asset</label>
                    <details class="relative">
                        <summary class="list-none flex w-full cursor-pointer items-center justify-between gap-3 rounded-lg border border-gray-300 px-4 py-3 text-sm uppercase focus:border-transparent focus:outline-none focus:ring-2 focus:ring-green-500">
                            <span class="truncate text-xs uppercase text-gray-700 dark:text-gray-100">
                                {{ empty($selectedAsset) ? 'Semua Asset' : implode(', ', $selectedAsset) }}
                            </span>
                            <span class="text-xs text-gray-400">Pilih</span>
                        </summary>
                        <div class="filter-menu absolute z-20 mt-2 max-h-64 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white dark:bg-gray-700 p-3 shadow-lg">
                            <div class="space-y-2">
                                @forelse($assetList as $asset)
                                    <label class="flex cursor-pointer items-center gap-2 text-sm text-gray-700 dark:text-gray-100">
                                        <input type="checkbox" name="asset[]" value="{{ $asset }}" {{ in_array($asset, $selectedAsset, true) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        <span class="text-xs uppercase">{{ $asset }}</span>
                                    </label>
                                @empty
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Belum ada data asset.</p>
                                @endforelse
                            </div>
                        </div>
                    </details>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Kategori</label>
                    <details class="relative">
                        <summary class="list-none flex w-full cursor-pointer items-center justify-between gap-3 rounded-lg border border-gray-300 px-4 py-3 text-sm uppercase focus:border-transparent focus:outline-none focus:ring-2 focus:ring-green-500">
                            <span class="truncate text-xs uppercase text-gray-700 dark:text-gray-100">
                                {{ empty($selectedKategori) ? 'Semua Kategori' : implode(', ', $selectedKategori) }}
                            </span>
                            <span class="text-xs text-gray-400">Pilih</span>
                        </summary>
                        <div class="filter-menu absolute z-20 mt-2 max-h-64 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white dark:bg-gray-700 p-3 shadow-lg">
                            <div class="space-y-2">
                                @foreach($kategoriOptions as $value => $label)
                                    <label class="flex cursor-pointer items-center gap-2 text-sm text-gray-700 dark:text-gray-100">
                                        <input type="checkbox" name="kategori[]" value="{{ $value }}" {{ in_array($value, $selectedKategori, true) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        <span class="text-xs uppercase">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </details>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Merk</label>
                    <details class="relative">
                        <summary class="list-none flex w-full cursor-pointer items-center justify-between gap-3 rounded-lg border border-gray-300 px-4 py-3 text-sm uppercase focus:border-transparent focus:outline-none focus:ring-2 focus:ring-green-500 ">
                            <span class="truncate text-xs uppercase text-gray-700 dark:text-gray-100">
                                {{ empty($selectedMerk) ? 'Semua Merk' : implode(', ', $selectedMerk) }}
                            </span>
                            <span class="text-xs text-gray-400">Pilih</span>
                        </summary>
                        <div class="filter-menu absolute z-20 mt-2 max-h-64 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white dark:bg-gray-700 p-3 shadow-lg">
                            <div class="space-y-2">
                                @forelse($merkList as $merk)
                                    <label class="flex cursor-pointer items-center gap-2 text-sm text-gray-700 dark:text-gray-100">
                                        <input type="checkbox" name="merk[]" value="{{ $merk }}" {{ in_array($merk, $selectedMerk, true) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        <span class="text-xs uppercase">{{ $merk }}</span>
                                    </label>
                                @empty
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Belum ada data merk.</p>
                                @endforelse
                            </div>
                        </div>
                    </details>
                </div>

                <div class="md:col-span-2 xl:col-span-5 flex flex-wrap gap-2 border-t pt-4">
                    <button type="submit" class="btn btn-success">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707L14 14v4l-4 2v-6L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Terapkan Filter
                    </button>
                    <a href="{{ route('laporan.index') }}" class="btn bg-gray-500 hover:bg-gray-600">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Bersihkan
                    </a>
                </div>
            </form>
        </section>

        <section class="rounded-xl bg-white dark:bg-gray-700 p-6 shadow-lg">
            <div class="mb-5">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Export</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Periode: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $periodeLabel }}</span></p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Filter: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $filterLabel }}</span></p>
            </div>

            <div class="space-y-3">
                <a href="{{ route('laporan.export', array_merge(['format' => 'excel'], request()->query())) }}" class="btn btn-success btn-block justify-between">
                    <span>Export Excel</span>
                    <svg class="w-4 md:w-5 h-4 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"></path>
                    </svg>
                </a>

                <a href="{{ route('laporan.export', array_merge(['format' => 'pdf'], request()->query())) }}" target="_blank" class="btn btn-danger btn-block justify-between">
                    <span>Export PDF</span>
                    <svg class="w-4 md:w-5 h-4 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </a>
            </div>
        </section>
    </div>

    <section class="mt-6 rounded-xl bg-white dark:bg-gray-700 p-6 shadow-lg">
        <div class="mb-5">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Isi Laporan Yang Diekspor</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Laporan ini dibuat sebagai rekap keseluruhan, bukan pengganti halaman dashboard.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-lg border border-gray-200 p-4">
                <p class="font-bold text-gray-800 dark:text-gray-100">Ringkasan Inventaris</p>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Total barang, tersedia, digunakan, pemeliharaan, tidak digunakan, dibawa vendor, dan distribusi aktif.</p>
            </div>
            <div class="rounded-lg border border-gray-200 p-4">
                <p class="font-bold text-gray-800 dark:text-gray-100">Stok Per Kategori</p>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Rekap PC, monitor, printer, scanner, dan lainnya berdasarkan status.</p>
            </div>
            <div class="rounded-lg border border-gray-200 p-4">
                <p class="font-bold text-gray-800 dark:text-gray-100">Barang Masuk</p>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Ringkasan dan detail barang masuk sesuai periode, asset, kategori, dan merk yang dipilih.</p>
            </div>
            <div class="rounded-lg border border-gray-200 p-4">
                <p class="font-bold text-gray-800 dark:text-gray-100">Distribusi Per Lokasi</p>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Jumlah perangkat yang sedang aktif dipakai di setiap lokasi.</p>
            </div>
            <div class="rounded-lg border border-gray-200 p-4">
                <p class="font-bold text-gray-800 dark:text-gray-100">Perlu Perhatian</p>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Barang pemeliharaan, tanpa detail perangkat, tanpa lokasi, dan data belum lengkap.</p>
            </div>
            <div class="rounded-lg border border-gray-200 p-4">
                <p class="font-bold text-gray-800 dark:text-gray-100">Data Pendukung</p>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Daftar item terkait untuk mempermudah pengecekan setelah laporan dibuka.</p>
            </div>
        </div>
    </section>
</div>
<style>
.dark .filter-summary {
        background-color: #374151;
        border-color: #f0e8e8;
        color: #f9fafb;
    }

    .dark .filter-menu {
        background-color: #2d2b35;
        border-color: #4b4855;
        color: #f9fafb;
    }

    .dark .filter-dropdown[open] > .filter-summary {
        background-color: #374151;
        border-color: #09ff00;
        box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.16);
    }

    .dark .filter-menu label:hover {
        background-color: #403d49;
    }
</style>

@endsection
