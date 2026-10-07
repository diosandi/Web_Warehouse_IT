@extends('layouts.app')

@section('content')
@php
    $checklistOptions = [
        'Nonaktifkan aplikasi latar belakang',
        'Bersihkan cache browser',
        'Cek storage dan bersihkan Storage Sense',
        'Cek RAM dan optimasi performa',
        'Cek antivirus dan firewall',
        'Cek koneksi jaringan dan fisik perangkat',
    ];
    $hasActiveFilter = request()->filled('gedung') || request()->filled('ruangan') || request()->filled('status_cek') || request()->filled('search') || request()->filled('periode');
@endphp

<br>
<div class="mx-auto w-full px-3 py-8 sm:px-4 lg:px-6 lg:py-12">
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Maintenance Bulanan</h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Pengecekan rutin PC dari data distribusi aktif</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('maintenance_berkala.export', array_merge(
                ['format' => 'excel'],
                request()->except('page')
            )) }}" class="btn btn-success btn-sm">
                Export Excel
            </a>

            <a href="{{ route('maintenance_berkala.export', array_merge(
                ['format' => 'pdf'],
                request()->except('page')
            )) }}" target="_blank" class="btn btn-danger btn-sm">
                Export PDF
            </a>
        </div>

    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg border-l-4 border-green-500 bg-green-100 p-4 text-green-700">
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-lg border-l-4 border-red-500 bg-red-100 p-4 text-red-700">
            <span class="font-medium">{{ $errors->first() }}</span>
        </div>
    @endif

    <details class="bg-white dark:bg-gray-700 rounded-xl shadow-lg mb-6 group" {{ $hasActiveFilter ? 'open' : '' }}>
        <summary class="list-none p-4 md:p-6 cursor-pointer flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                <h2 class="text-lg md:text-xl font-bold text-gray-800 dark:text-gray-100">Filter & Cari PC</h2>
            </div>
            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 transition duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </summary>
        <div class="px-4 md:px-6 pb-4 md:pb-6">
            <form method="GET" class="space-y-4">
                <!-- Search Bar -->
                <div>
                    <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-100 mb-2">🔍 Cari PC / User / Divisi</label>
                    <input type="text" id="search" name="search" autocomplete="off" value="{{ $search }}" placeholder="SN, merk, asset, user atau divisi"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-xs uppercase">
                    <input type="hidden" name="maintenance_berkala_id" id="maintenance_berkala_id_hidden" value="{{ request('maintenance_berkala_id') }}" >
                    <div id="suggestions" class="absolute z-10 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-500 rounded-lg mt-1 shadow-lg hidden max-h-56 overflow-auto text-xs uppercase"></div>
                    @if(request('search'))
                        <span class="absolute right-3 top-3 text-gray-400 text-sm font-semibold ">{{ strlen(request('search')) }} char</span>
                    @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <label class="block text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-100 mb-2">Periode</label>
                        <input type="month" name="periode" value="{{ $periode }}"
                            class="w-full px-3 md:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10 text-xs uppercase">
                    </div>
                    <div>
                        <label class="block text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-100 mb-2">Gedung</label>
                            <select name="gedung" id="gedung" class="w-full px-3 md:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10 text-xs uppercase">
                                <option value="">Semua</option>
                                @foreach($gedungs as $gedung)
                                    <option value="{{ $gedung }}" {{ request('gedung') == $gedung ? 'selected' : '' }}>
                                        {{ $gedung }}
                                    </option>
                                @endforeach
                            </select>
                    </div>

                    <div>
                        <label class="block text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-100 mb-2">Ruangan</label>
                            <select name="ruangan" id="ruangan" data-selected="{{ request('ruangan') }}" class="w-full px-3 md:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10 text-xs uppercase">
                                <option value="">Semua</option>
                            </select>
                    </div>
                    <div>
                        <label class="block text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-100 mb-2">
                            Status Pengecekan
                        </label>

                        <select name="status_cek"
                            class="w-full px-3 md:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition text-xs uppercase">
                            <option value="">Semua PC</option>
                            <option value="sudah_dicek" {{ request('status_cek') === 'sudah_dicek' ? 'selected' : '' }}>
                                Sudah Dicek
                            </option>
                            <option value="belum_dicek" {{ request('status_cek') === 'belum_dicek' ? 'selected' : '' }}>
                                Belum Dicek
                            </option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">
                    <button type="submit" class="btn btn-success btn-block">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="hidden sm:inline">Cari</span>
                    </button>
                    <a href="{{ route('maintenance_berkala.index') }}" class="btn bg-gray-500 hover:bg-gray-600 btn-block">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span class="hidden sm:inline">Bersihkan</span>
                    </a>
                </div>

                <!-- Active filter -->
                @if ($hasActiveFilter)
                    <div class="text-xs md:text-sm text-gray-600 pt-3 border-t border-gray-200">
                        <span class="font-semibold text-gray-700 dark:text-gray-100 block mb-2">Filter aktif:</span>
                        <div class="flex flex-wrap gap-2">
                            @if(request('search'))
                                @php
                                    $searchQuery = request()->query();
                                    unset($searchQuery['search'], $searchQuery['item_id']);
                                @endphp
                                <span class="bg-yellow-100 text-yellow-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                                    <span>Cari: <strong>"{{ request('search') }}"</strong></span>
                                    <a href="{{ route('maintenance_berkala.index', $searchQuery) }}" class="hover:text-yellow-900 font-bold text-lg leading-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </a>
                                </span>
                            @endif
                            @if(request('periode'))
                                @php
                                    $periodeQuery = request()->query();
                                    unset($periodeQuery['periode']);
                                @endphp
                                <span class="bg-purple-100 text-purple-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                                    <span>Periode: <strong>{{ request('periode') }}</strong></span>
                                    <a href="{{ route('maintenance_berkala.index', $periodeQuery) }}" class="hover:text-purple-900 font-bold text-lg leading-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </a>
                                </span>
                            @endif
                            @if(request('gedung'))
                                @php
                                    $gedungQuery = request()->query();
                                    unset($gedungQuery['gedung']);
                                @endphp
                                <span class="bg-blue-100 text-blue-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                                    <span>Gedung: <strong>{{ request('gedung') }}</strong></span>
                                    <a href="{{ route('maintenance_berkala.index', $gedungQuery) }}" class="hover:text-blue-900 font-bold text-lg leading-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </a>
                                </span>
                            @endif
                            @if(request('ruangan'))
                                @php
                                    $ruanganQuery = request()->query();
                                    unset($ruanganQuery['ruangan']);
                                @endphp
                                <span class="bg-indigo-100 text-indigo-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                                    <span>Ruangan: <strong>{{ request('ruangan') }}</strong></span>
                                    <a href="{{ route('maintenance_berkala.index', $ruanganQuery) }}" class="hover:text-indigo-900 font-bold text-lg leading-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </a>
                                </span>
                            @endif
                            @if(request('status_cek'))
                                @php
                                    $statusCekQuery = request()->query();
                                    unset($statusCekQuery['status_cek']);
                                @endphp
                                <span class="bg-green-100 text-green-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                                    <span>Status: <strong>{{ request('status_cek') === 'sudah_dicek' ? 'Sudah Dicek' : 'Belum Dicek' }}</strong></span>
                                    <a href="{{ route('maintenance_berkala.index', $statusCekQuery) }}" class="hover:text-green-900 font-bold text-lg leading-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </a>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </details>

    <div class="overflow-hidden rounded-xl bg-white shadow-lg dark:bg-gray-700">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 bg-gray-50 px-4 py-4 dark:border-gray-700 dark:bg-gray-700 md:px-6">
            <div class="text-sm text-gray-600 dark:text-gray-300">
                <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $distributionItems->total() }}</span>
                PC aktif ditemukan
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-300">Periode: <span class="font-semibold">{{ $periode }}</span></div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-green-600 to-green-700">
                    <tr>
                        <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white">No</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white">PC</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white">User</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white">Lokasi</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white">Status Bulan Ini</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white">Terakhir Dicek</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white">History</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider text-white">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-600 dark:bg-gray-700">
                    @forelse($distributionItems as $distributionItem)
                        @php
                            $item = $distributionItem->item;
                            $distribution = $distributionItem->distribution;
                            $maintenanceBulanIni = $distributionItem->maintenanceBerkalas->firstWhere('periode_bulan', $periode);
                            $maintenanceTerakhir = $distributionItem->maintenanceBerkalas->first();
                            $maintenanceHistory = $distributionItem->maintenanceBerkalas->map(function ($maintenance) {
                                return [
                                    'periode_bulan' => $maintenance->periode_bulan,
                                    'tanggal_cek' => $maintenance->tanggal_cek ? \App\Support\DateFormatter::date($maintenance->tanggal_cek) : '-',
                                    'status' => $maintenance->status === 'perlu_perbaikan' ? 'Perlu Perbaikan' : 'Sudah Dicek',
                                    'kondisi' => $maintenance->kondisi === 'perlu_perbaikan' ? 'Perlu Perbaikan' : 'Baik',
                                    'teknisi' => $maintenance->teknisi ?: '-',
                                    'catatan' => $maintenance->catatan ?: '-',
                                    'checklist' => $maintenance->checklist ?: [],
                                ];
                            })->values();
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900 dark:text-gray-50">
                                {{ $distributionItems->firstItem() + $loop->index }}
                            </td>
                            <td class="px-4 py-4 text-sm uppercase text-gray-900 dark:text-gray-50">
                                <div class="font-semibold">{{ $item?->serial_number ?? '-' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-300">{{ $item?->merk ?? '-' }} / Asset: {{ $item?->asset ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4 text-sm uppercase text-gray-900 dark:text-gray-50">
                                <div>{{ $distribution?->user?->name ?? $distribution?->nama_user ?? '-' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-300">{{ $distribution?->divisi ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4 text-sm uppercase text-gray-900 dark:text-gray-50">
                                {{ $distribution?->location?->gedung ?? '-' }} - {{ $distribution?->location?->ruangan ?? '-' }}
                            </td>
                            <td class="px-4 py-4 text-sm">
                                @if($maintenanceBulanIni)
                                    @if($maintenanceBulanIni->status === 'perlu_perbaikan')
                                        <span class="rounded bg-red-100 px-2 py-1 text-xs font-semibold text-red-700">Perlu Perbaikan</span>
                                    @else
                                        <span class="rounded bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">Sudah Dicek</span>
                                    @endif
                                @else
                                    <span class="rounded bg-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-700">Belum Dicek</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-50">
                                {{ $maintenanceTerakhir?->tanggal_cek ? \App\Support\DateFormatter::date($maintenanceTerakhir->tanggal_cek) : '-' }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-50">
                                <div class="mb-2">
                                    <span class="font-semibold">{{ $distributionItem->maintenanceBerkalas->count() }}</span>
                                    <span class="text-gray-500 dark:text-gray-300">kali dicek</span>
                                </div>
                                <button type="button"
                                    class="btn btn-secondary btn-sm"
                                    onclick="openHistoryModal(@js($item?->serial_number ?? '-'), @js($item?->merk ?? '-'), @js($maintenanceHistory))">
                                    History
                                </button>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <button type="button"
                                    class="btn btn-success btn-sm"
                                    onclick="openMaintenanceModal({{ $distributionItem->id }}, @js($item?->serial_number ?? '-'), @js($item?->merk ?? '-'))">
                                    Catat
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-sm font-semibold text-gray-600 dark:text-gray-300">
                                Belum ada PC aktif dari data distribusi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white dark:bg-gray-700 px-3 md:px-4 py-4 border-t border-gray-200 overflow-x-auto">
            <div class="location-pagination flex justify-center md:justify-end">
                {{ $distributionItems->links() }}
            </div>
        </div>
    </div>
</div>

<div id="historyModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
    <div class="flex min-h-screen w-full items-center justify-center p-4">
        <div class="w-full max-w-3xl rounded-lg bg-white p-6 shadow-2xl dark:bg-gray-700">
            <div class="mb-5 flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-50">History Maintenance</h2>
                    <p id="historyDeviceText" class="mt-1 text-sm uppercase text-gray-500 dark:text-gray-300"></p>
                </div>
                <button type="button" onclick="closeHistoryModal()" class="btn bg-gray-500 hover:bg-gray-600 btn-sm">Tutup</button>
            </div>

            <div id="historyEmpty" class="hidden rounded-lg bg-yellow-100 p-4 text-sm font-semibold text-yellow-700">
                PC ini belum punya history maintenance berkala.
            </div>

            <div id="historyList" class="max-h-[65vh] space-y-3 overflow-y-auto"></div>
        </div>
    </div>
</div>

<div id="maintenanceModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
    <div class="flex min-h-screen w-full items-center justify-center p-4">
        <div class="w-full max-w-xl rounded-lg bg-white p-6 shadow-2xl dark:bg-gray-700">
            <h2 class="mb-1 text-xl font-bold text-gray-900 dark:text-gray-50">Catat Maintenance Bulanan</h2>
            <p id="maintenanceDeviceText" class="mb-5 text-sm uppercase text-gray-500 dark:text-gray-300"></p>

            <form method="POST" action="{{ route('maintenance_berkala.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="distribution_item_id" id="distributionItemId">
                <input type="hidden" name="periode_bulan" value="{{ $periode }}">

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-100">Tanggal Cek</label>
                        <input type="date" name="tanggal_cek" value="{{ now()->toDateString() }}"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm" required>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-100">Kondisi</label>
                        <select name="kondisi" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm" required>
                            <option value="baik">Baik</option>
                            <option value="perlu_perbaikan">Perlu Perbaikan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-100">Checklist</label>
                    <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                        @foreach($checklistOptions as $option)
                            <label class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm dark:border-gray-600 dark:text-gray-100">
                                <input type="checkbox" name="checklist[]" value="{{ $option }}" class="rounded text-green-600">
                                <span>{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-100">Teknisi</label>
                    <input type="text" name="teknisi" value="{{ auth()->user()->name ?? '' }}"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-100">Catatan</label>
                    <textarea name="catatan" rows="3" placeholder="Contoh: suhu normal, storage aman, perlu ganti fan"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeMaintenanceModal()" class="btn bg-gray-500 hover:bg-gray-600">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    /* Custom pagination styling untuk responsif */
    .pagination {
        display: flex;
        gap: 0.25rem;
        flex-wrap: wrap;
        justify-content: center;
    }
    .pagination a,
    .pagination span {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    .dark .pagination a,
    .dark .pagination span {
        color: #f9fafb;
        border-color: #4b5563;
    }

    .dark .location-pagination nav,
    .dark .location-pagination p {
        color: #d1d5db;
    }

    .dark .location-pagination span,
    .dark .location-pagination a {
        border-color: #4b5563 !important;
    }

    .dark .location-pagination a {
        background-color: #374151 !important;
        color: #f9fafb !important;
    }

    .dark .location-pagination a:hover {
        background-color: #1f2937 !important;
        color: #ffffff !important;
    }

    .dark .location-pagination span[aria-current="page"] span {
        background-color: #1f2937 !important;
        border-color: #6b7280 !important;
        color: #ffffff !important;
        font-weight: 700;
    }

    .dark .location-pagination span[aria-disabled="true"] span,
    .dark .location-pagination span:not([aria-current]) {
        background-color: #374151 !important;
        color: #9ca3af !important;
    }
</style>

<script>
$(document).ready(function() {
    var $input = $('#search');
    var $suggestions = $('#suggestions');
    var $hidden = $('#maintenance_berkala_id_hidden');
    var $gedung = $('#gedung');
    var $ruangan = $('#ruangan');
    var roomsByBuilding = @json($locationsByGedung);
    var searchDelay;

    function populateRooms() {
        var selectedRoom = String($ruangan.data('selected') || '');
        var rooms = roomsByBuilding[$gedung.val()] || [];

        $ruangan.empty().append($('<option>', { value: '', text: 'Semua' }));

        $.each(rooms, function(_, room) {
            $ruangan.append($('<option>', { value: room, text: room }));
        });

        $ruangan.val(selectedRoom);
    }

    $gedung.on('change', function() {
        $ruangan.data('selected', '');
        populateRooms();
    });

    populateRooms();

    $input.on('input', function() {
        var query = $(this).val().trim();
        $hidden.val(''); // reset hidden
        clearTimeout(searchDelay);

        if (query.length < 3) {
            $suggestions.empty().hide();
            return;
        }

        searchDelay = setTimeout(function() {
            $.ajax({
                url: '{{ route('maintenance_berkala.search') }}',
                data: { q: query },
                dataType: 'json',
                success: function(data) {
                    if (data.length === 0) {
                        $suggestions.html('<div class="px-3 py-2 text-gray-500">Tidak ada hasil</div>').show();
                        return;
                    }
                    var html = '';
                    $.each(data, function(i, maintenance_berkala) {
                        html += '<div class="px-3 py-2 cursor-pointer hover:bg-green-100 dark:hover:bg-gray-600" data-id="'+maintenance_berkala.id+'" data-text="'+maintenance_berkala.text+'">'+maintenance_berkala.text+'</div>';
                    });
                    $suggestions.html(html).show();
                }
            });
        }, 250);
    });

    $suggestions.on('click', 'div[data-id]', function() {
        var id = $(this).data('id');
        var text = $(this).data('text');
        $input.val(text);
        $hidden.val(id);
        $suggestions.hide();
    });

    // Hide suggestions on click outside
    $(document).on('mousedown', function(e) {
        if (!$(e.target).closest('#search, #suggestions').length) {
            $suggestions.hide();
        }
    });
});

function openMaintenanceModal(distributionItemId, serialNumber, merk)
{
    document.getElementById('distributionItemId').value = distributionItemId;
    document.getElementById('maintenanceDeviceText').textContent = serialNumber + ' / ' + merk;
    document.getElementById('maintenanceModal').classList.remove('hidden');
    document.getElementById('maintenanceModal').classList.add('flex');
}

function closeMaintenanceModal()
{
    document.getElementById('maintenanceModal').classList.add('hidden');
    document.getElementById('maintenanceModal').classList.remove('flex');
}

function escapeHtml(value)
{
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function openHistoryModal(serialNumber, merk, histories)
{
    const historyList = document.getElementById('historyList');
    const historyEmpty = document.getElementById('historyEmpty');

    document.getElementById('historyDeviceText').textContent = serialNumber + ' / ' + merk;
    historyList.innerHTML = '';

    if (!histories.length) {
        historyEmpty.classList.remove('hidden');
    } else {
        historyEmpty.classList.add('hidden');

        histories.forEach(function (history) {
            const statusClass = history.status === 'Perlu Perbaikan'
                ? 'bg-red-100 text-red-700'
                : 'bg-green-100 text-green-700';
            const status = escapeHtml(history.status);
            const periodeBulan = escapeHtml(history.periode_bulan);
            const tanggalCek = escapeHtml(history.tanggal_cek);
            const kondisi = escapeHtml(history.kondisi);
            const teknisi = escapeHtml(history.teknisi);
            const catatan = escapeHtml(history.catatan);

            const checklist = history.checklist.length
                ? history.checklist.map(function (item) {
                    return `<span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-700">${escapeHtml(item)}</span>`;
                }).join(' ')
                : '<span class="text-sm text-gray-500">Checklist belum diisi</span>';

            historyList.insertAdjacentHTML('beforeend', `
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-600">
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-gray-50">${periodeBulan}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-300">Tanggal cek: ${tanggalCek}</div>
                        </div>
                        <span class="rounded px-2 py-1 text-xs font-semibold ${statusClass}">${status}</span>
                    </div>
                    <div class="grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
                        <div class="text-gray-700 dark:text-gray-100"><span class="font-semibold">Kondisi:</span> ${kondisi}</div>
                        <div class="text-gray-700 dark:text-gray-100"><span class="font-semibold">Teknisi:</span> ${teknisi}</div>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">${checklist}</div>
                    <div class="mt-3 text-sm text-gray-700 dark:text-gray-100"><span class="font-semibold">Catatan:</span> ${catatan}</div>
                </div>
            `);
        });
    }

    document.getElementById('historyModal').classList.remove('hidden');
    document.getElementById('historyModal').classList.add('flex');
}

function closeHistoryModal()
{
    document.getElementById('historyModal').classList.add('hidden');
    document.getElementById('historyModal').classList.remove('flex');
}
</script>
@endsection
