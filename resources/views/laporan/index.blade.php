@extends('layouts.app')

@section('content')
<br>
<div class="container mx-auto px-4 py-12">
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Ekspor Laporan Warehouse IT</h1>
        <p class="text-sm text-gray-600 mt-1">Buat file laporan keseluruhan warehouse IT dalam format Excel atau PDF.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <section class="xl:col-span-2 rounded-xl bg-white p-6 shadow-lg">
            <div class="mb-5">
                <h2 class="text-xl font-bold text-gray-800">Periode Laporan</h2>
                <p class="mt-1 text-sm text-gray-500">Filter ini dipakai untuk bagian barang masuk. Ringkasan stok dan distribusi aktif tetap menampilkan kondisi saat ini.</p>
            </div>

            <form method="GET" action="{{ route('laporan.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Tanggal Dari</label>
                    <input type="date" name="tanggal_dari" value="{{ $tanggalDari }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Tanggal Sampai</label>
                    <input type="date" name="tanggal_sampai" value="{{ $tanggalSampai }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div class="md:col-span-2 flex flex-wrap gap-2 border-t pt-4">
                    <button type="submit" class="btn btn-success">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707L14 14v4l-4 2v-6L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Terapkan Periode
                    </button>
                    <a href="{{ route('laporan.index') }}" class="btn btn-secondary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Bersihkan
                    </a>
                </div>
            </form>
        </section>

        <section class="rounded-xl bg-white p-6 shadow-lg">
            <div class="mb-5">
                <h2 class="text-xl font-bold text-gray-800">Export</h2>
                <p class="mt-1 text-sm text-gray-500">Periode: <span class="font-semibold text-gray-700">{{ $periodeLabel }}</span></p>
            </div>

            <div class="space-y-3">
                <a href="{{ route('laporan.export', array_merge(['format' => 'excel'], request()->query())) }}" class="btn btn-success btn-block justify-between">
                    <span>Export Excel</span>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m4 5H5a2 2 0 01-2-2V6a2 2 0 012-2h8l6 6v8a2 2 0 01-2 2z"></path>
                    </svg>
                </a>

                <a href="{{ route('laporan.export', array_merge(['format' => 'pdf'], request()->query())) }}" target="_blank" class="btn btn-danger btn-block justify-between">
                    <span>Export PDF</span>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m4 5H5a2 2 0 01-2-2V6a2 2 0 012-2h8l6 6v8a2 2 0 01-2 2z"></path>
                    </svg>
                </a>
            </div>
        </section>
    </div>

    <section class="mt-6 rounded-xl bg-white p-6 shadow-lg">
        <div class="mb-5">
            <h2 class="text-xl font-bold text-gray-800">Isi Laporan Yang Diekspor</h2>
            <p class="mt-1 text-sm text-gray-500">Laporan ini dibuat sebagai rekap keseluruhan, bukan pengganti halaman dashboard.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-lg border border-gray-200 p-4">
                <p class="font-bold text-gray-800">Ringkasan Inventaris</p>
                <p class="mt-1 text-sm text-gray-600">Total barang, tersedia, digunakan, pemeliharaan, tidak digunakan, dan distribusi aktif.</p>
            </div>
            <div class="rounded-lg border border-gray-200 p-4">
                <p class="font-bold text-gray-800">Stok Per Kategori</p>
                <p class="mt-1 text-sm text-gray-600">Rekap PC, monitor, printer, scanner, dan lainnya berdasarkan status.</p>
            </div>
            <div class="rounded-lg border border-gray-200 p-4">
                <p class="font-bold text-gray-800">Barang Masuk</p>
                <p class="mt-1 text-sm text-gray-600">Ringkasan dan detail barang masuk sesuai periode yang dipilih.</p>
            </div>
            <div class="rounded-lg border border-gray-200 p-4">
                <p class="font-bold text-gray-800">Distribusi Per Lokasi</p>
                <p class="mt-1 text-sm text-gray-600">Jumlah perangkat yang sedang aktif dipakai di setiap lokasi.</p>
            </div>
            <div class="rounded-lg border border-gray-200 p-4">
                <p class="font-bold text-gray-800">Perlu Perhatian</p>
                <p class="mt-1 text-sm text-gray-600">Barang pemeliharaan, tanpa detail perangkat, tanpa lokasi, dan data belum lengkap.</p>
            </div>
            <div class="rounded-lg border border-gray-200 p-4">
                <p class="font-bold text-gray-800">Data Pendukung</p>
                <p class="mt-1 text-sm text-gray-600">Daftar item terkait untuk mempermudah pengecekan setelah laporan dibuka.</p>
            </div>
        </div>
    </section>
</div>
@endsection
