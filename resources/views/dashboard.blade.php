@extends('layouts.app')

@section('content')
@php
    $isManager = Auth::user()->canManageIssueReports();
    $hasActiveFilter = request()->filled('search') || request()->filled('status') || request()->filled('priority');
    $statusClasses = [
        'open' => 'bg-yellow-100 text-yellow-800',
        'in_progress' => 'bg-blue-100 text-blue-800',
        'resolved' => 'bg-green-100 text-green-800',
        'closed' => 'bg-gray-100 text-gray-800',
    ];
    $priorityClasses = [
        'low' => 'bg-gray-100 text-gray-800',
        'normal' => 'bg-blue-100 text-blue-800',
        'high' => 'bg-orange-100 text-orange-800',
        'urgent' => 'bg-red-100 text-red-800',
    ];
@endphp
<br>
<div class="distribution-page mx-auto w-full px-3 py-8 sm:px-4 lg:px-6 lg:py-12">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Dashboard</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Selamat datang di Warehouse IT RSCM</p>
    </div>

    <!-- Welcome Message -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-8 text-white gap-6 mb-8">
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

    {{-- card 1 --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-300 text-sm font-medium">Total Barang</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2">{{number_format($summary['total_items'])}}</h3>
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
        <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-300 text-sm font-medium">Tersedia</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2">{{number_format($summary['available'])}}</h3>
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
        <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-300 text-sm font-medium">Digunakan</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2">{{number_format($summary['used'])}}</h3>
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
        <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-300 text-sm font-medium">Pemeliharaan</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2">{{number_format($summary['maintenance'])}}</h3>
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
        <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6 border-l-4 border-gray-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-300 text-sm font-medium">Tidak Digunakan</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2">{{number_format($summary['retired'])}}</h3>
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
        <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-300 text-sm font-medium">Dibawa Vendor</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2">{{number_format($summary['vendor'])}}</h3>
                </div>
                <div class="bg-purple-100 p-4 rounded-lg">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6h4m-7 4h10m-9 8h8a4 4 0 004-4v-4a2 2 0 00-2-2h-1.5A2.5 2.5 0 0014 5.5h-4A2.5 2.5 0 007.5 8H6a2 2 0 00-2 2v4a4 4 0 004 4z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>
        {{-- card 7 --}}
        <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-300 text-sm font-medium">Aktif Distribusi</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2">{{number_format($summary['active_distributions'])}}</h3>
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
        {{-- card 8 --}}
        <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6 border-l-4 border-cyan-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-300 text-sm font-medium">Barang Masuk</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2">{{ number_format($summary['barang_masuk']) }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total data barang masuk</p>
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
        {{-- card 9 --}}
        <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-300 text-sm font-medium">Masuk Bulan Ini</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2">{{ number_format($summary['barang_masuk_bulan_ini']) }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Data {{ now()->translatedFormat('F Y') }}</p>
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
        {{-- card 10 --}}
        <a href="{{ route('issue_reports.index', ['status' => 'open']) }}" class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6 border-l-4 border-orange-500 hover:shadow-xl transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-300 text-sm font-medium">Antrian Kendala</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2">{{ number_format($summary['issue_reports_open']) }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Laporan baru dan diproses</p>
                </div>
                <div class="bg-orange-100 p-4 rounded-lg">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
                    </svg>
                </div>
            </div>
        </a>
    </div>

<!-- Chart Status Barang + Stok Per Kategori-->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        <!-- Chart Status Barang + Stok Per Kategori-->
        <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Status Barang</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Perbandingan status seluruh barang</p>
                </div>
            </div>

            <div class="relative h-72 w-full flex items-center justify-center overflow-hidden">
                <!-- Chart Canvas -->
                <canvas id="statusBarangChart" class="relative z-10 h-full w-full cursor-default" role="img" tabindex="0"></canvas>

                <!-- Overlay Teks (Posisinya dinaikkan agar pas di lubang Doughnut) -->
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none select-none"
                    style="transform: translateY(-28px); z-index: 5;">

                    <span class="text-gray-900 dark:text-gray-100"
                        style="font-family: Arial, sans-serif; font-weight: 800; font-size: 34px; line-height: 1;">
                        {{ number_format($summary['total_items'], 0, ',', '.') }}
                    </span>

                    <span class="text-gray-500 dark:text-gray-400"
                        style="font-family: Arial, sans-serif; font-weight: 700; font-size: 11px; margin-top: 4px; letter-spacing: 0.05em; text-transform: uppercase;">
                        Total Barang
                    </span>
                </div>
            </div>
        </div>

        {{-- Stok Per Kategori --}}
        <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Stok Per Kategori</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Ringkasan barang berdasarkan kategori</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                            <th class="pb-3 font-semibold dark:text-gray-300">Kategori</th>
                            <th class="pb-3 text-center font-semibold dark:text-gray-300">Total</th>
                            <th class="pb-3 text-center font-semibold dark:text-gray-300">Tersedia</th>
                            <th class="pb-3 text-center font-semibold dark:text-gray-300">Dipakai</th>
                            <th class="pb-3 text-center font-semibold dark:text-gray-300">Pemeliharaan</th>
                            <th class="pb-3 text-center font-semibold dark:text-gray-300">Tidak Digunakan</th>
                            <th class="pb-3 text-center font-semibold dark:text-gray-300">Vendor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-600">
                        @foreach($stokPerKategori as $stok)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-600">
                                <td class="py-3 font-semibold text-gray-800 dark:text-gray-100">
                                    {{ $stok['kategori'] }}
                                </td>
                                <td class="py-3 text-center font-bold text-gray-800 dark:text-gray-100">
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
                                <td class="py-3 text-center">
                                    <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700">
                                        {{ number_format($stok['vendor']) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

{{-- Dashboard Asset --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
        {{-- Stok Berdasarkan Asset --}}
        <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Stok Berdasarkan Asset</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Ringkasan kepemilikan barang berdasarkan asset.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[860px] text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                            <th class="pb-3 font-semibold dark:text-gray-300">Asset</th>
                            <th class="pb-3 text-center font-semibold dark:text-gray-300">Total</th>
                            <th class="pb-3 text-center font-semibold dark:text-gray-300">Tersedia</th>
                            <th class="pb-3 text-center font-semibold dark:text-gray-300">Dipakai</th>
                            <th class="pb-3 text-center font-semibold dark:text-gray-300">Pemeliharaan</th>
                            <th class="pb-3 text-center font-semibold dark:text-gray-300">Tidak Digunakan</th>
                            <th class="pb-3 text-center font-semibold dark:text-gray-300">Vendor</th>
                            <th class="pb-3 text-center font-semibold dark:text-gray-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-600">
                        @forelse($stokPerAsset as $asset)
                            <tr class="cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-600"
                                data-dashboard-asset-url="{{ $asset['url'] }}"
                                role="link"
                                tabindex="0"
                                title="Lihat item asset {{ $asset['asset'] }}">
                                <td class="py-3 font-semibold uppercase text-gray-800 dark:text-gray-100">{{ $asset['asset'] }}</td>
                                <td class="py-3 text-center font-bold text-gray-900 dark:text-gray-100">{{ number_format($asset['total']) }}</td>
                                <td class="py-3 text-center">
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        {{ number_format($asset['available']) }}
                                    </span>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                                        {{ number_format($asset['used']) }}
                                    </span>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                        {{ number_format($asset['maintenance']) }}
                                    </span>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                        {{ number_format($asset['retired']) }}
                                    </span>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700">
                                        {{ number_format($asset['vendor']) }}
                                    </span>
                                </td>
                                <td class="py-3 text-center">
                                    <a href="{{ $asset['url'] }}" class="btn btn-primary btn-sm">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-sm font-medium text-gray-500">
                                    Belum ada data asset.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Distribusi Aktif Berdasarkan Asset --}}
        <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Distribusi Aktif Berdasarkan Asset</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Barang fisik yang sedang dipakai, dikelompokkan berdasarkan asset.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                            <th class="pb-3 font-semibold dark:text-gray-300">Asset</th>
                            <th class="pb-3 text-center font-semibold dark:text-gray-300">PC</th>
                            <th class="pb-3 text-center font-semibold dark:text-gray-300">Monitor</th>
                            <th class="pb-3 text-center font-semibold dark:text-gray-300">Printer Kertas</th>
                            <th class="pb-3 text-center font-semibold dark:text-gray-300">Printer Barcode</th>
                            <th class="pb-3 text-center font-semibold dark:text-gray-300">Scanner</th>
                            <th class="pb-3 text-center font-semibold dark:text-gray-300">Lainnya</th>
                            <th class="pb-3 text-center font-semibold dark:text-gray-300">Total</th>
                            <th class="pb-3 text-center font-semibold dark:text-gray-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-600">
                        @forelse($distribusiAktifPerAsset as $asset)
                            <tr class="cursor-pointer hover:bg-green-50 dark:hover:bg-gray-600"
                                data-dashboard-asset-url="{{ $asset['url'] }}"
                                role="link"
                                tabindex="0"
                                title="Lihat distribusi aktif asset {{ $asset['asset'] }}">
                                <td class="py-3 font-semibold uppercase text-gray-800 dark:text-gray-100">{{ $asset['asset'] }}</td>
                                <td class="py-3 text-center text-gray-700 dark:text-gray-100">{{ number_format($asset['PC']) }}</td>
                                <td class="py-3 text-center text-gray-700 dark:text-gray-100">{{ number_format($asset['Monitor']) }}</td>
                                <td class="py-3 text-center text-gray-700 dark:text-gray-100">{{ number_format($asset['Printer Kertas']) }}</td>
                                <td class="py-3 text-center text-gray-700 dark:text-gray-100">{{ number_format($asset['Printer Barcode']) }}</td>
                                <td class="py-3 text-center text-gray-700 dark:text-gray-100">{{ number_format($asset['Scanner']) }}</td>
                                <td class="py-3 text-center text-gray-700 dark:text-gray-100">{{ number_format($asset['Lainnya']) }}</td>
                                <td class="py-3 text-center font-bold text-gray-900 dark:text-gray-100">{{ number_format($asset['total']) }}</td>
                                <td class="py-3 text-center">
                                    <a href="{{ $asset['url'] }}" class="btn btn-success btn-sm">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-sm font-medium text-gray-500">
                                    Belum ada distribusi aktif berdasarkan asset.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

{{-- Distribusi Terbaru --}}
    <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Distribusi Terbaru</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">5 data distribusi terakhir</p>
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
                        <th class="pb-3 font-semibold dark:text-gray-300">User</th>
                        <th class="pb-3 font-semibold dark:text-gray-300">Barang / SN</th>
                        <th class="pb-3 font-semibold dark:text-gray-300">Lokasi</th>
                        <th class="pb-3 font-semibold dark:text-gray-300">Tanggal</th>
                        <th class="pb-3 font-semibold dark:text-gray-300">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-600">
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

                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-600">
                            <td class="py-4 pr-4">
                                <p class="font-semibold uppercase text-gray-800 dark:text-gray-100">
                                    {{ $distribusi->user?->name ?? $distribusi->nama_user ?? '-' }}
                                </p>
                                <p class="text-xs uppercase text-gray-500 dark:text-gray-400">
                                    {{ $distribusi->divisi ?? '-' }}
                                </p>
                            </td>

                            <td class="py-4 pr-4">
                                <p class="font-semibold text-gray-800 dark:text-gray-100">
                                    {{ $itemText ?: '-' }}
                                </p>

                                @if($sisaItem > 0)
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        +{{ $sisaItem }} barang lainnya
                                    </p>
                                @endif
                            </td>

                            <td class="py-4 pr-4 uppercase text-gray-700 dark:text-gray-300">
                                {{ $distribusi->location->gedung ?? '-' }}
                                -
                                {{ $distribusi->location->ruangan ?? '-' }}
                            </td>

                            <td class="py-4 pr-4 text-gray-700 dark:text-gray-300">
                               {{ \App\Support\DateFormatter::date($distribusi->tanggal_distribusi) }}
                            </td>

                            <td class="py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
                                Belum ada data distribusi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

{{-- Antrian Kendala Terbaru --}}
    <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Antrian Kendala Terbaru</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Laporan user yang masih baru atau sedang diproses.</p>
            </div>

            <a href="{{ route('issue_reports.index') }}" class="btn btn-success btn-sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6m-6 4h6m-6 4h4m6-8v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2h6l6 6z"></path>
                </svg>
                Lihat Semua
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[920px] text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                        <th class="pb-3 font-semibold dark:text-gray-300">Tiket</th>
                        <th class="pb-3 font-semibold dark:text-gray-300">Pelapor</th>
                        <th class="pb-3 font-semibold dark:text-gray-300">Kendala</th>
                        <th class="pb-3 font-semibold dark:text-gray-300">Perangkat</th>
                        <th class="pb-3 font-semibold dark:text-gray-300">Lokasi</th>
                        <th class="pb-3 font-semibold dark:text-gray-300">Prioritas</th>
                        <th class="pb-3 font-semibold dark:text-gray-300">Status</th>
                        <th class="pb-3 font-semibold dark:text-gray-300">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-600">
                    @forelse($laporanKendalaTerbaru as $report)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-600">
                            <td class="py-4 pr-4 font-mono font-semibold text-gray-800 dark:text-gray-100">{{ $report->ticket_number }}</td>
                            <td class="py-4 pr-4">
                                <p class="font-semibold uppercase text-gray-800 dark:text-gray-100">{{ $report->reporter->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $report->created_at->format('d/m/Y H:i') }}</p>
                            </td>
                            <td class="py-4 pr-4 text-gray-800 dark:text-gray-100">{{ $report->title }}</td>
                            <td class="py-4 pr-4">
                                <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $report->item->kategori ?? '-' }}</p>
                                <p class="text-xs font-mono text-gray-500 dark:text-gray-400">{{ $report->item->serial_number ?? '-' }}</p>
                            </td>
                            <td class="py-4 pr-4 uppercase text-gray-700 dark:text-gray-300">
                                {{ $report->location->gedung ?? '-' }} - {{ $report->location->ruangan ?? '-' }}
                            </td>
                            <td class="py-4 pr-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $priorityClasses[$report->priority] ?? 'bg-gray-100 text-gray-800 dark:text-gray-100' }}">
                                    {{ $report->priority_label }}
                                </span>
                            </td>
                            <td class="py-4 pr-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClasses[$report->status] ?? 'bg-gray-100 text-gray-800 dark:text-gray-100' }}">
                                    {{ $report->status_label }}
                                </span>
                            </td>
                            <td class="py-4">
                                <a href="{{ route('issue_reports.show', $report) }}" class="btn btn-primary btn-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
                                Belum ada antrian kendala.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

{{-- Distribusi Aktif Per Lokasi --}}
    <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6 mb-8">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Distribusi Aktif Per Lokasi</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Perangkat yang sedang dipakai, dikelompokkan berdasarkan lokasi.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                        <th class="pb-3 font-semibold dark:text-gray-300">Gedung</th>
                        <th class="pb-3 font-semibold dark:text-gray-300">Ruangan</th>
                        <th class="pb-3 text-center font-semibold dark:text-gray-300">PC</th>
                        <th class="pb-3 text-center font-semibold dark:text-gray-300">Monitor</th>
                        <th class="pb-3 text-center font-semibold dark:text-gray-300">Printer Kertas</th>
                        <th class="pb-3 text-center font-semibold dark:text-gray-300">Printer Barcode</th>
                        <th class="pb-3 text-center font-semibold dark:text-gray-300">Scanner</th>
                        <th class="pb-3 text-center font-semibold dark:text-gray-300">Lainnya</th>
                        <th class="pb-3 text-center font-semibold dark:text-gray-300">Total</th>
                        <th class="pb-3 text-center font-semibold dark:text-gray-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-600">
                    @forelse($distribusiPerLokasi as $lokasi)
                        <tr class="cursor-pointer hover:bg-green-50 dark:hover:bg-gray-600"
                            data-dashboard-location-url="{{ $lokasi['url'] }}"
                            role="link"
                            tabindex="0"
                            title="Lihat distribusi aktif lokasi ini">
                            <td class="py-3 font-semibold uppercase text-gray-800 dark:text-gray-100">{{ $lokasi['gedung'] }}</td>
                            <td class="py-3 uppercase text-gray-700 dark:text-gray-100">{{ $lokasi['ruangan'] }}</td>
                            <td class="py-3 text-center dark:text-gray-100">{{ number_format($lokasi['PC']) }}</td>
                            <td class="py-3 text-center dark:text-gray-100">{{ number_format($lokasi['Monitor']) }}</td>
                            <td class="py-3 text-center dark:text-gray-100">{{ number_format($lokasi['Printer Kertas']) }}</td>
                            <td class="py-3 text-center dark:text-gray-100">{{ number_format($lokasi['Printer Barcode']) }}</td>
                            <td class="py-3 text-center dark:text-gray-100">{{ number_format($lokasi['Scanner']) }}</td>
                            <td class="py-3 text-center dark:text-gray-100">{{ number_format($lokasi['Lainnya']) }}</td>
                            <td class="py-3 text-center font-bold text-gray-900 dark:text-gray-100">{{ number_format($lokasi['total']) }}</td>
                            <td class="py-3 text-center">
                                <a href="{{ $lokasi['url'] }}" class="btn btn-primary btn-sm">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-sm font-medium text-gray-500">
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
        <div class="lg:col-span-2 bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Perlu Perhatian</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Data yang perlu dicek atau dilengkapi</p>
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
                            'purple' => [
                                'bg' => 'bg-purple-50',
                                'border' => 'border-purple-500',
                                'text' => 'text-purple-700',
                                'badge' => 'bg-purple-100 text-purple-700',
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
        <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Aksi Cepat</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Shortcut untuk pekerjaan utama</p>
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
                class="btn bg-gray-500 hover:bg-gray-600 btn-block justify-between">
                    <span>Lihat Semua Distribusi</span>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-dashboard-location-url], [data-dashboard-asset-url]').forEach(function (row) {
        const rowUrl = row.dataset.dashboardLocationUrl || row.dataset.dashboardAssetUrl;

        if (!rowUrl) {
            return;
        }

        row.addEventListener('click', function (event) {
            if (event.target.closest('a, button')) {
                return;
            }

            window.location.href = rowUrl;
        });

        row.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                window.location.href = rowUrl;
            }
        });
    });

    const statusChartElement = document.getElementById('statusBarangChart');

    if (!statusChartElement) {
        return;
    }

    if (window.statusBarangDoughnutChart && typeof window.statusBarangDoughnutChart.destroy === 'function') {
        window.statusBarangDoughnutChart.destroy();
    }

    window.statusBarangDoughnutChart = renderDoughnutChart(statusChartElement, [
        { label: 'Tersedia', value: @json((int) $summary['available']), color: '#22c55e' },
        { label: 'Digunakan', value: @json((int) $summary['used']), color: '#eab308' },
        { label: 'Pemeliharaan', value: @json((int) $summary['maintenance']), color: '#ef4444' },
        { label: 'Tidak Digunakan', value: @json((int) $summary['retired']), color: '#6b7280' },
        { label: 'Dibawa Vendor', value: @json((int) $summary['vendor']), color: '#a855f7' }
    ]);
    window.myDoughnutChart = window.statusBarangDoughnutChart;
});

function renderDoughnutChart(canvas, rawItems) {
    const context = canvas.getContext('2d');
    const parent = canvas.parentElement || canvas;
    const items = rawItems.map(function (item) {
        return {
            label: item.label,
            value: Math.max(Number(item.value) || 0, 0),
            color: item.color
        };
    });
    let activeIndex = null;
    let lastPointerEvent = null;
    let animationFrame = null;
    let resizeTimer = null;
    let resizeObserver = null;
    let chartState = {
        centerX: 0,
        centerY: 0,
        radius: 0,
        innerRadius: 0,
        slices: []
    };
    const tooltip = document.createElement('div');

    Object.assign(tooltip.style, {
        position: 'fixed',
        zIndex: '9999',
        display: 'none',
        pointerEvents: 'none',
        padding: '8px 10px',
        borderRadius: '8px',
        font: '600 12px Arial, sans-serif',
        boxShadow: '0 12px 28px rgba(15, 23, 42, 0.28)',
        whiteSpace: 'nowrap'
    });
    document.body.appendChild(tooltip);

    function getTotal() {
        return items.reduce(function (sum, item) {
            return sum + item.value;
        }, 0);
    }

    function formatNumber(value) {
        return value.toLocaleString('id-ID');
    }

    function getThemeColors() {
        const isDark = document.documentElement.classList.contains('dark');

        return {
            emptyFill: isDark ? '#4b5563' : '#e5e7eb',
            legendText: isDark ? '#e5e7eb' : '#374151',
            sliceBorder: isDark ? '#374151' : '#ffffff',
            shadow: isDark ? 'rgba(0, 0, 0, 0.42)' : 'rgba(17, 24, 39, 0.28)',
            tooltipBg: isDark ? 'rgba(31, 41, 55, 0.98)' : 'rgba(17, 24, 39, 0.96)',
            tooltipText: '#ffffff',
            tooltipMuted: isDark ? '#d1d5db' : '#e5e7eb'
        };
    }

    function applyTooltipTheme(themeColors) {
        tooltip.style.background = themeColors.tooltipBg;
        tooltip.style.color = themeColors.tooltipText;
        tooltip.style.boxShadow = '0 12px 28px ' + themeColors.shadow;
    }

    function buildLegendRows(width) {
        context.font = '600 12px system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';
        const rows = [];
        let currentRow = [];
        let currentWidth = 0;
        const maxWidth = Math.max(width - 32, 180);

        items.forEach(function (item) {
            const itemWidth = context.measureText(item.label).width + 42;

            if (currentRow.length && currentWidth + itemWidth > maxWidth) {
                rows.push(currentRow);
                currentRow = [];
                currentWidth = 0;
            }

            currentRow.push({ item: item, width: itemWidth });
            currentWidth += itemWidth;
        });

        if (currentRow.length) {
            rows.push(currentRow);
        }

        return rows;
    }

    function drawLegend(rows, width, height, themeColors) {
        const rowHeight = 22;
        const startY = height - (rows.length * rowHeight) + 8;

        context.font = '600 12px system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';
        context.textAlign = 'left';
        context.textBaseline = 'middle';

        rows.forEach(function (row, rowIndex) {
            const rowWidth = row.reduce(function (sum, entry) {
                return sum + entry.width;
            }, 0);
            let x = Math.max((width - rowWidth) / 2, 12);
            const y = startY + (rowIndex * rowHeight);

            row.forEach(function (entry) {
                context.beginPath();
                context.fillStyle = entry.item.color;
                context.arc(x + 6, y, 5, 0, Math.PI * 2);
                context.fill();

                context.fillStyle = themeColors.legendText;
                context.fillText(entry.item.label, x + 18, y);
                x += entry.width;
            });
        });
    }

    function normalizeAngle(angle) {
        const fullCircle = Math.PI * 2;

        return ((angle % fullCircle) + fullCircle) % fullCircle;
    }

    function isAngleBetween(angle, startAngle, endAngle) {
        const normalizedAngle = normalizeAngle(angle);
        const normalizedStart = normalizeAngle(startAngle);
        const normalizedEnd = normalizeAngle(endAngle);

        if (normalizedStart <= normalizedEnd) {
            return normalizedAngle >= normalizedStart && normalizedAngle <= normalizedEnd;
        }

        return normalizedAngle >= normalizedStart || normalizedAngle <= normalizedEnd;
    }

    function getHoveredSliceIndex(event) {
        const rect = canvas.getBoundingClientRect();
        const x = event.clientX - rect.left;
        const y = event.clientY - rect.top;
        const distanceX = x - chartState.centerX;
        const distanceY = y - chartState.centerY;
        const distance = Math.sqrt((distanceX * distanceX) + (distanceY * distanceY));

        if (distance < chartState.innerRadius || distance > chartState.radius + 14) {
            return null;
        }

        const angle = Math.atan2(distanceY, distanceX);
        const slice = chartState.slices.find(function (sliceItem) {
            return sliceItem.fullCircle || isAngleBetween(angle, sliceItem.startAngle, sliceItem.endAngle);
        });

        return slice ? slice.index : null;
    }

    function positionTooltip(event) {
        const margin = 14;
        const offset = 14;
        const rect = tooltip.getBoundingClientRect();
        let left = event.clientX + offset;
        let top = event.clientY + offset;

        if (left + rect.width + margin > window.innerWidth) {
            left = event.clientX - rect.width - offset;
        }

        if (top + rect.height + margin > window.innerHeight) {
            top = event.clientY - rect.height - offset;
        }

        tooltip.style.left = Math.max(margin, left) + 'px';
        tooltip.style.top = Math.max(margin, top) + 'px';
    }

    function showTooltip(event, item) {
        const total = getTotal();
        const percentage = total > 0 ? ((item.value / total) * 100).toFixed(1) : '0.0';
        const themeColors = getThemeColors();
        const titleRow = document.createElement('div');
        const dot = document.createElement('span');
        const label = document.createElement('span');
        const value = document.createElement('div');

        applyTooltipTheme(themeColors);
        tooltip.innerHTML = '';
        Object.assign(titleRow.style, {
            display: 'flex',
            alignItems: 'center',
            gap: '8px',
            marginBottom: '3px'
        });
        Object.assign(dot.style, {
            width: '9px',
            height: '9px',
            borderRadius: '999px',
            background: item.color,
            boxShadow: '0 0 0 2px rgba(255, 255, 255, 0.25)'
        });

        label.textContent = item.label;
        value.textContent = formatNumber(item.value) + ' barang (' + percentage + '%)';
        label.style.color = themeColors.tooltipText;
        value.style.color = themeColors.tooltipMuted;
        value.style.fontWeight = '600';

        titleRow.appendChild(dot);
        titleRow.appendChild(label);
        tooltip.appendChild(titleRow);
        tooltip.appendChild(value);
        tooltip.style.display = 'block';
        positionTooltip(event);
    }

    function hideTooltip() {
        tooltip.style.display = 'none';
    }

    function scheduleDraw() {
        if (animationFrame !== null) {
            window.cancelAnimationFrame(animationFrame);
        }

        animationFrame = window.requestAnimationFrame(function () {
            animationFrame = null;
            draw();
        });
    }

    function handleThemeOrLayoutChange() {
        scheduleDraw();

        if (activeIndex !== null && lastPointerEvent) {
            showTooltip(lastPointerEvent, items[activeIndex]);
        }
    }

    function draw() {
        const bounds = parent.getBoundingClientRect();
        const width = Math.max(Math.floor(bounds.width || canvas.clientWidth || 320), 280);
        const height = Math.max(Math.floor(bounds.height || canvas.clientHeight || 288), 240);
        const ratio = Math.min(Math.max(window.devicePixelRatio || 1, 2), 3);
        const displayWidth = Math.floor(width * ratio);
        const displayHeight = Math.floor(height * ratio);
        const total = getTotal();
        const themeColors = getThemeColors();
        const legendRows = buildLegendRows(width);
        const legendHeight = Math.max(46, legendRows.length * 22 + 18);
        const chartHeight = Math.max(height - legendHeight, 140);
        const centerX = width / 2;
        const centerY = chartHeight / 2 + 10;
        const radius = Math.max(Math.min(width, chartHeight) * 0.50, 60);
        const innerRadius = radius * 0.65;
        const slices = [];
        let startAngle = -Math.PI / 2;

        if (total > 0) {
            items.forEach(function (item, index) {
                if (item.value === 0) {
                    return;
                }

                const sliceAngle = (item.value / total) * Math.PI * 2;
                const endAngle = startAngle + sliceAngle;

                slices.push({
                    index: index,
                    item: item,
                    startAngle: startAngle,
                    endAngle: endAngle,
                    middleAngle: startAngle + (sliceAngle / 2),
                    fullCircle: sliceAngle >= (Math.PI * 2) - 0.0001
                });

                startAngle = endAngle;
            });
        }

        chartState = {
            centerX: centerX,
            centerY: centerY,
            radius: radius,
            innerRadius: innerRadius,
            slices: slices
        };

        if (canvas.width !== displayWidth || canvas.height !== displayHeight) {
            canvas.width = displayWidth;
            canvas.height = displayHeight;
            canvas.style.width = width + 'px';
            canvas.style.height = height + 'px';
        }

        context.setTransform(ratio, 0, 0, ratio, 0, 0);
        context.imageSmoothingEnabled = true;
        context.imageSmoothingQuality = 'high';
        context.clearRect(0, 0, width, height);

        if (total === 0) {
            context.beginPath();
            context.arc(centerX, centerY, radius, 0, Math.PI * 2);
            context.arc(centerX, centerY, innerRadius, Math.PI * 2, 0, true);
            context.closePath();
            context.fillStyle = themeColors.emptyFill;
            context.fill();
        } else {
            const drawSlice = function (sliceItem) {
                const isActive = sliceItem.index === activeIndex;
                const lift = isActive ? 6 : 0;
                const outerRadius = radius + (isActive ? 3 : 0);
                const hoverInnerRadius = innerRadius + (isActive ? 2 : 0);
                const offsetX = Math.cos(sliceItem.middleAngle) * lift;
                const offsetY = Math.sin(sliceItem.middleAngle) * lift;

                context.save();
                if (isActive) {
                    context.shadowColor = themeColors.shadow;
                    context.shadowBlur = 16;
                    context.shadowOffsetY = 6;
                }
                context.beginPath();
                context.arc(centerX + offsetX, centerY + offsetY, outerRadius, sliceItem.startAngle, sliceItem.endAngle);
                context.arc(centerX + offsetX, centerY + offsetY, hoverInnerRadius, sliceItem.endAngle, sliceItem.startAngle, true);
                context.closePath();
                context.fillStyle = sliceItem.item.color;
                context.fill();
                context.lineWidth = isActive ? 5 : 4;
                context.strokeStyle = themeColors.sliceBorder;
                context.stroke();
                context.restore();
            };

            slices.filter(function (sliceItem) {
                return sliceItem.index !== activeIndex;
            }).forEach(drawSlice);

            slices.filter(function (sliceItem) {
                return sliceItem.index === activeIndex;
            }).forEach(drawSlice);
        }

        // context.textAlign = 'center';
        // context.fillStyle = '#111827';
        // context.font = '800 34px Arial, sans-serif';
        // context.fillText(total.toLocaleString('id-ID'), centerX, centerY - 4);
        // context.fillStyle = '#6b7280';
        // context.font = '700 14px Arial, sans-serif';
        // context.fillText('Total Barang', centerX, centerY + 24);

        drawLegend(legendRows, width, height, themeColors);
        canvas.setAttribute('aria-label', items.map(function (item) {
            const percentage = total > 0 ? ((item.value / total) * 100).toFixed(1) : '0.0';

            return item.label + ': ' + item.value.toLocaleString('id-ID') + ' barang (' + percentage + '%)';
        }).join(', '));
    }

    function updateActiveSlice(event) {
        const nextActiveIndex = getHoveredSliceIndex(event);

        lastPointerEvent = event;
        canvas.style.cursor = nextActiveIndex === null ? 'default' : 'pointer';

        if (nextActiveIndex === null) {
            hideTooltip();
        } else {
            showTooltip(event, items[nextActiveIndex]);
        }

        if (nextActiveIndex !== activeIndex) {
            activeIndex = nextActiveIndex;
            draw();
        }
    }

    function clearActiveSlice() {
        canvas.style.cursor = 'default';
        hideTooltip();

        if (activeIndex !== null) {
            activeIndex = null;
            draw();
        }
    }

    canvas.addEventListener('pointermove', updateActiveSlice);
    canvas.addEventListener('pointerdown', updateActiveSlice);
    canvas.addEventListener('pointerleave', clearActiveSlice);
    canvas.addEventListener('blur', clearActiveSlice);

    canvas.addEventListener('keydown', function (event) {
        if (!['ArrowLeft', 'ArrowRight', 'Enter', ' '].includes(event.key) || !chartState.slices.length) {
            return;
        }

        event.preventDefault();

        const currentSlicePosition = chartState.slices.findIndex(function (sliceItem) {
            return sliceItem.index === activeIndex;
        });
        let nextSlicePosition = currentSlicePosition;

        if (event.key === 'ArrowLeft') {
            nextSlicePosition = currentSlicePosition <= 0 ? chartState.slices.length - 1 : currentSlicePosition - 1;
        } else if (event.key === 'ArrowRight' || currentSlicePosition === -1) {
            nextSlicePosition = currentSlicePosition >= chartState.slices.length - 1 ? 0 : currentSlicePosition + 1;
        }

        activeIndex = chartState.slices[nextSlicePosition].index;
        draw();
    });

    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(scheduleDraw, 150);
    });
    window.addEventListener('warehouse-theme-changed', handleThemeOrLayoutChange);
    window.addEventListener('warehouse-layout-changed', handleThemeOrLayoutChange);

    if ('ResizeObserver' in window) {
        resizeObserver = new ResizeObserver(scheduleDraw);
        resizeObserver.observe(parent);
    }

    draw();

    return {
        update: scheduleDraw,
        destroy: function () {
            if (animationFrame !== null) {
                window.cancelAnimationFrame(animationFrame);
            }

            clearTimeout(resizeTimer);
            window.removeEventListener('warehouse-theme-changed', handleThemeOrLayoutChange);
            window.removeEventListener('warehouse-layout-changed', handleThemeOrLayoutChange);

            if (resizeObserver) {
                resizeObserver.disconnect();
            }

            tooltip.remove();
        }
    };
}
</script>
@endsection
