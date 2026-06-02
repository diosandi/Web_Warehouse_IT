@extends('layouts.app')

@section('content')
<br>
<div class="container mx-auto px-4 py-12">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-600 mt-1">Selamat datang di Warehouse IT RSCM</p>
    </div>
    {{-- card 1 --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Barang</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-2">{{number_format($summary['total_items'])}}</h3> 
                </div>
                <div class="bg-blue-100 p-4 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                        </path>
                    </svg>
                </div>
            </div>
        </div>
        {{-- card 2 --}}
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Tersedia</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-2">{{number_format($summary['available'])}}</h3> 
                </div>
                <div class="bg-green-100 p-4 rounded-lg">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>
        {{-- card 3 --}}    
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>           
                    <p class="text-gray-500 text-sm font-medium">Digunakan</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-2">{{number_format($summary['used'])}}</h3> 
                </div>
                <div class="bg-yellow-100 p-4 rounded-lg">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>      
        {{-- card 4 --}}
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>           
                    <p class="text-gray-500 text-sm font-medium">Pemeliharaan</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-2">{{number_format($summary['maintenance'])}}</h3>    
                </div>
                <div class="bg-red-100 p-4 rounded-lg">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.83-5.83M11.42 15.17l2.47-2.47a3.375 3.375 0 00-4.773-4.773L6.75 10.293m4.67 4.877L6.75 10.293m0 0L3 6.543V3h3.543l3.75 3.75">
                        </path>
                    </svg>
                </div>
            </div>
        </div>
        {{-- card 5 --}}
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-gray-500">
            <div class="flex items-center justify-between">
                <div>           
                    <p class="text-gray-500 text-sm font-medium">Tidak Digunakan</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-2">{{number_format($summary['retired'])}}</h3>    
                </div>
                <div class="bg-gray-100 p-4 rounded-lg">
                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.25 7.5l-.625 10.632A2.25 2.25 0 0117.378 20.25H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M9.75 11.25v6M14.25 11.25v6M4.5 7.5h15M10.5 4.5h3a1.5 1.5 0 011.5 1.5v1.5h-6V6a1.5 1.5 0 011.5-1.5z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>      
        {{-- card 6 --}}
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>           
                    <p class="text-gray-500 text-sm font-medium">Aktif Distribusi</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-2">{{number_format($summary['active_distributions'])}}</h3>    
                </div>
                <div class="bg-purple-100 p-4 rounded-lg">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.25 18.75a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM18.75 18.75a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM3 6.75h11.25v9H3v-9zM14.25 9.75h3.75L21 13.5v2.25h-6.75v-6z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>
        {{-- card 7 --}}
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-cyan-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Barang Masuk</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($summary['barang_masuk']) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Total data barang masuk</p>
                </div>
                <div class="bg-cyan-100 p-4 rounded-lg">
                    <svg class="w-8 h-8 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v10m0 0l-4-4m4 4l4-4M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2">
                        </path>
                    </svg>
                </div>
            </div>
        </div>
        {{-- card 8 --}}
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Masuk Bulan Ini</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($summary['barang_masuk_bulan_ini']) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Data {{ now()->translatedFormat('F Y') }}</p>
                </div>
                <div class="bg-indigo-100 p-4 rounded-lg">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3M5 11h14M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    
<!-- Chart Status Barang + Stok Per Kategori-->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        <!-- Chart Status Barang + Stok Per Kategori-->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Status Barang</h2>
                    <p class="text-sm text-gray-500 mt-1">Perbandingan status seluruh barang</p>
                </div>
            </div>

            <div class="relative h-72">
                <canvas id="statusBarangChart"></canvas>
            </div>
        </div>

        {{-- Stok Per Kategori --}}
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Stok Per Kategori</h2>
                    <p class="text-sm text-gray-500 mt-1">Ringkasan barang berdasarkan kategori</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                            <th class="pb-3 font-semibold">Kategori</th>
                            <th class="pb-3 text-center font-semibold">Total</th>
                            <th class="pb-3 text-center font-semibold">Tersedia</th>
                            <th class="pb-3 text-center font-semibold">Dipakai</th>
                            <th class="pb-3 text-center font-semibold">Pemeliharaan</th>
                            <th class="pb-3 text-center font-semibold">Tidak Digunakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($stokPerKategori as $stok)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 font-semibold text-gray-800">
                                    {{ $stok['kategori'] }}
                                </td>
                                <td class="py-3 text-center font-bold text-gray-800">
                                    {{ number_format($stok['total']) }}
                                </td>
                                <td class="py-3 text-center">
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        {{ number_format($stok['available']) }}
                                    </span>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                                        {{ number_format($stok['used']) }}
                                    </span>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                        {{ number_format($stok['maintenance']) }}
                                    </span>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                        {{ number_format($stok['retired']) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

{{-- Distribusi Terbaru --}}
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Distribusi Terbaru</h2>
                <p class="text-sm text-gray-500 mt-1">5 data distribusi terakhir</p>
            </div>

            <a href="{{ route('distribution.index') }}"
            class="btn btn-success btn-sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6m-6 4h6m-6 4h4m6-8v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2h6l6 6z"></path>
                </svg>
                Lihat Semua
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[780px] text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                        <th class="pb-3 font-semibold">User</th>
                        <th class="pb-3 font-semibold">Barang / SN</th>
                        <th class="pb-3 font-semibold">Lokasi</th>
                        <th class="pb-3 font-semibold">Tanggal</th>
                        <th class="pb-3 font-semibold">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($distribusiTerbaru as $distribusi)
                        @php
                            $items = $distribusi->distributionItems
                                ->map(fn ($distributionItem) => $distributionItem->item)
                                ->filter();

                            $itemText = $items->take(2)->map(function ($item) {
                                return ($item->kategori ?? '-') . ' / ' . ($item->serial_number ?? '-');
                            })->implode(', ');

                            $sisaItem = max($items->count() - 2, 0);

                            $statusClass = $distribusi->status === 'dipakai'
                                ? 'bg-blue-100 text-blue-700'
                                : 'bg-gray-100 text-gray-700';

                            $statusText = $distribusi->status === 'dipakai'
                                ? 'Dipakai'
                                : 'Dikembalikan';
                        @endphp

                        <tr class="hover:bg-gray-50">
                            <td class="py-4 pr-4">
                                <p class="font-semibold uppercase text-gray-800">
                                    {{ $distribusi->nama_user ?? '-' }}
                                </p>
                                <p class="text-xs uppercase text-gray-500">
                                    {{ $distribusi->divisi ?? '-' }}
                                </p>
                            </td>

                            <td class="py-4 pr-4">
                                <p class="font-semibold text-gray-800">
                                    {{ $itemText ?: '-' }}
                                </p>

                                @if($sisaItem > 0)
                                    <p class="mt-1 text-xs text-gray-500">
                                        +{{ $sisaItem }} barang lainnya
                                    </p>
                                @endif
                            </td>

                            <td class="py-4 pr-4 uppercase text-gray-700">
                                {{ $distribusi->location->gedung ?? '-' }}
                                -
                                {{ $distribusi->location->ruangan ?? '-' }}
                            </td>

                            <td class="py-4 pr-4 text-gray-700">
                               {{ $distribusi->tanggal_distribusi ? \Carbon\Carbon::parse($distribusi->tanggal_distribusi)->format('d-m-Y') : '-' }}
                            </td>

                            <td class="py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-sm font-medium text-gray-500">
                                Belum ada data distribusi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

{{-- Distribusi Aktif Per Lokasi --}}
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800">Distribusi Aktif Per Lokasi</h2>
            <p class="text-sm text-gray-500 mt-1">Perangkat yang sedang dipakai, dikelompokkan berdasarkan lokasi.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                        <th class="pb-3 font-semibold">Gedung</th>
                        <th class="pb-3 font-semibold">Ruangan</th>
                        <th class="pb-3 text-center font-semibold">PC</th>
                        <th class="pb-3 text-center font-semibold">Monitor</th>
                        <th class="pb-3 text-center font-semibold">Printer Kertas</th>
                        <th class="pb-3 text-center font-semibold">Printer Barcode</th>
                        <th class="pb-3 text-center font-semibold">Scanner</th>
                        <th class="pb-3 text-center font-semibold">Lainnya</th>
                        <th class="pb-3 text-center font-semibold">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($distribusiPerLokasi as $lokasi)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 font-semibold uppercase text-gray-800">{{ $lokasi['gedung'] }}</td>
                            <td class="py-3 uppercase text-gray-700">{{ $lokasi['ruangan'] }}</td>
                            <td class="py-3 text-center">{{ number_format($lokasi['PC']) }}</td>
                            <td class="py-3 text-center">{{ number_format($lokasi['Monitor']) }}</td>
                            <td class="py-3 text-center">{{ number_format($lokasi['Printer Kertas']) }}</td>
                            <td class="py-3 text-center">{{ number_format($lokasi['Printer Barcode']) }}</td>
                            <td class="py-3 text-center">{{ number_format($lokasi['Scanner']) }}</td>
                            <td class="py-3 text-center">{{ number_format($lokasi['Lainnya']) }}</td>
                            <td class="py-3 text-center font-bold text-gray-900">{{ number_format($lokasi['total']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-sm font-medium text-gray-500">
                                Belum ada distribusi aktif.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

{{-- Perlu Perhatian + Quick Action --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Perlu Perhatian --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800">Perlu Perhatian</h2>
                <p class="text-sm text-gray-500 mt-1">Data yang perlu dicek atau dilengkapi</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($perluPerhatian as $warning)
                    @php
                        $colorClass = [
                            'red' => [
                                'bg' => 'bg-red-50',
                                'border' => 'border-red-500',
                                'text' => 'text-red-700',
                                'badge' => 'bg-red-100 text-red-700',
                            ],
                            'yellow' => [
                                'bg' => 'bg-yellow-50',
                                'border' => 'border-yellow-500',
                                'text' => 'text-yellow-700',
                                'badge' => 'bg-yellow-100 text-yellow-700',
                            ],
                            'blue' => [
                                'bg' => 'bg-blue-50',
                                'border' => 'border-blue-500',
                                'text' => 'text-blue-700',
                                'badge' => 'bg-blue-100 text-blue-700',
                            ],
                            'gray' => [
                                'bg' => 'bg-gray-50',
                                'border' => 'border-gray-500',
                                'text' => 'text-gray-700',
                                'badge' => 'bg-gray-100 text-gray-700',
                            ],
                        ][$warning['color']];
                    @endphp

                    <a href="{{ $warning['url'] }}"
                    class="{{ $colorClass['bg'] }} border-l-4 {{ $colorClass['border'] }} rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-bold {{ $colorClass['text'] }}">
                                    {{ $warning['title'] }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $warning['description'] }}
                                </p>
                            </div>

                            <span class="rounded-full px-3 py-1 text-sm font-bold {{ $colorClass['badge'] }}">
                                {{ number_format($warning['count']) }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Quick Action --}}
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800">Aksi Cepat</h2>
                <p class="text-sm text-gray-500 mt-1">Shortcut untuk pekerjaan utama</p>
            </div>

            <div class="space-y-3">
                <a href="{{ route('barang_masuk.create') }}"
                class="btn btn-success btn-block justify-between">
                    <span>Tambah Barang Masuk</span>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </a>

                <a href="{{ route('items.create') }}"
                class="btn btn-primary btn-block justify-between">
                    <span>Tambah Item</span>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </a>

                <a href="{{ route('distribution.create') }}"
                class="btn btn-warning btn-block justify-between">
                    <span>Buat Distribusi</span>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </a>

                <a href="{{ route('items.index', ['status' => 'maintenance']) }}"
                class="btn btn-danger btn-block justify-between">
                    <span>Lihat Pemeliharaan</span>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>

                <a href="{{ route('distribution.index') }}"
                class="btn btn-secondary btn-block justify-between">
                    <span>Lihat Semua Distribusi</span>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            </div>
        </div>

    </div>

<!-- Welcome Message -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-8 text-white">
        <div class="flex items-center gap-4">
            <div class="bg-white/20 p-4 rounded-lg">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold mb-2">Selamat Datang, {{ Auth::user()->name }}!</h2>
                <p class="text-green-100"><strong>Berhasi Masuk!</strong></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusChartElement = document.getElementById('statusBarangChart');

    if (!statusChartElement) {
        return;
    }

    new Chart(statusChartElement, {
        type: 'doughnut',
        data: {
            labels: [
                'Tersedia',
                'Digunakan',
                'Pemeliharaan',
                'Tidak Digunakan'
            ],
            datasets: [{
                data: [
                    {{ $summary['available'] }},
                    {{ $summary['used'] }},
                    {{ $summary['maintenance'] }},
                    {{ $summary['retired'] }}
                ],
                backgroundColor: [
                    '#22c55e',
                    '#eab308',
                    '#ef4444',
                    '#6b7280'
                ],
                borderColor: '#ffffff',
                borderWidth: 4,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 18,
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((sum, item) => sum + item, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;

                            return label + ': ' + value + ' barang (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
