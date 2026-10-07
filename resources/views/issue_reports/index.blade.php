@extends('layouts.app')

@section('content')
@php
    $isManager = Auth::user()->canManageIssueReports();
    $hasActiveFilter = request()->filled('search') || request()->filled('status') || request()->filled('priority') || request()->filled('issue_category');
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
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $isManager ? 'Antrian Laporan Kendala' : 'Laporan Kendala Saya' }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $isManager ? 'Pantau dan tindak lanjuti laporan dari user.' : 'Pantau laporan kendala perangkat yang kamu kirim.' }}</p>
        </div>

        <a href="{{ route('issue_reports.create') }}" class="btn btn-success">
            <svg class="w-4 md:w-5 h-4 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Buat Laporan
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Baru</p>
            <h3 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2">{{ number_format($summary['open']) }}</h3>
        </div>
        <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Diproses</p>
            <h3 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2">{{ number_format($summary['in_progress']) }}</h3>
        </div>
        <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Selesai</p>
            <h3 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2">{{ number_format($summary['resolved']) }}</h3>
        </div>
        <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6 border-l-4 border-gray-500">
            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Ditutup</p>
            <h3 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2">{{ number_format($summary['closed']) }}</h3>
        </div>
    </div>

    @if(false && ! $isManager)
        <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="px-4 md:px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-lg md:text-xl font-bold text-gray-800 dark:text-gray-100">Status Maintenance PC Saya</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Periode bulan ini: {{ $maintenancePeriode }}</p>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-300">
                    <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $maintenanceDevices->count() }}</span>
                    PC aktif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-green-600 to-green-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">PC</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Lokasi</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Status Bulan Ini</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Terakhir Dicek</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Tracking</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                        @forelse($maintenanceDevices as $distributionItem)
                            @php
                                $item = $distributionItem->item;
                                $distribution = $distributionItem->distribution;
                                $maintenanceBulanIni = $distributionItem->maintenanceBerkalas->firstWhere('periode_bulan', $maintenancePeriode);
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
                            <tr class="hover:bg-green-50 dark:hover:bg-gray-800 transition duration-150">
                                <td class="px-6 py-4 text-sm uppercase">
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $item?->serial_number ?? '-' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item?->merk ?? '-' }} / Asset: {{ $item?->asset ?? '-' }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm uppercase text-gray-700 dark:text-gray-100">
                                    {{ $distribution?->location?->gedung ?? '-' }} - {{ $distribution?->location?->ruangan ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($maintenanceBulanIni)
                                        @if($maintenanceBulanIni->status === 'perlu_perbaikan')
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Perlu Perbaikan</span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Sudah Dicek</span>
                                        @endif
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Belum Dicek</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-100">
                                    {{ $maintenanceTerakhir?->tanggal_cek ? \App\Support\DateFormatter::date($maintenanceTerakhir->tanggal_cek) : '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-gray-700 dark:text-gray-100">
                                            {{ $distributionItem->maintenanceBerkalas->count() }} kali dicek
                                        </span>
                                        <button type="button"
                                            class="btn btn-secondary btn-sm"
                                            onclick="openUserMaintenanceHistory(@js($item?->serial_number ?? '-'), @js($item?->merk ?? '-'), @js($maintenanceHistory))">
                                            History
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Belum ada PC aktif yang terhubung ke akun ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <details class="bg-white dark:bg-gray-700 rounded-xl shadow-lg mb-6 group" {{ $hasActiveFilter ? 'open' : '' }}>
        <summary class="list-none p-4 md:p-6 cursor-pointer flex items-center justify-between gap-3">
             <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                <h2 class="text-lg md:text-xl font-bold text-gray-800 dark:text-gray-100">Filter Laporan</h2>
             </div>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 transition duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </span>
        </summary>

        <div class="px-4 md:px-6 pb-4 md:pb-6">
            <form method="GET" action="{{ route('issue_reports.index') }}" class="space-y-4">
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-100 mb-2">🔍 Cari</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tiket, judul, SN, pelapor" class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10 text-xs uppercase ">
                            <input type="hidden" name="location_id" id="location_id_hidden" >
                        <div id="suggestions" class="absolute z-10 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-500 rounded-lg mt-1 shadow-lg hidden max-h-56 overflow-auto text-xs uppercase"></div>
                        @if(request('search'))
                            <span class="absolute right-3 top-3 text-gray-400 text-sm font-semibold">{{ strlen(request('search')) }} char</span>
                        @endif
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tekan Enter atau klik Cari untuk mencari di semua field</p>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5">
                    <div>
                        <label class="block text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-100 mb-2">Kategori</label>
                        <select name="issue_category" class="w-full px-3 md:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10 text-xs uppercase">
                            <option class="filter-option" value="">Semua</option>
                            @foreach($categoryOptions as $category => $label)
                                <option class="filter-option" value="{{ $category }}" {{ request('issue_category') === $category ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-100 mb-2">Status</label>
                        <select name="status" class="w-full px-3 md:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10 text-xs uppercase">
                            <option value="">Semua</option>
                            @foreach($statusOptions as $status => $label)
                                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-100 mb-2">Prioritas</label>
                        <select name="priority" class="w-full px-3 md:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10 text-xs uppercase">
                            <option value="">Semua</option>
                            @foreach($priorityOptions as $priority => $label)
                                <option value="{{ $priority }}" {{ request('priority') === $priority ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5">
                    <button type="submit" class="btn btn-success">Cari</button>
                    <a href="{{ route('issue_reports.index') }}" class="btn bg-gray-500 hover:bg-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>Bersihkan
                    </a>
                </div>
            </form>
        </div>
    </details>

    <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg overflow-hidden">
        <div class="px-4 md:px-6 py-3 md:py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-500 flex justify-between items-center flex-wrap gap-2">
            <div class="text-xs md:text-sm text-gray-600 dark:text-gray-400">
                <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $reports->total() }}</span>
                <span>Laporan Ditemukan</span>
            </div>
            <div class="text-xs md:text-sm text-gray-600 dark:text-gray-300">
                Halaman <span class="font-semibold">{{ $reports->currentPage() }}</span> dari <span class="font-semibold">{{ $reports->lastPage() }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 ">
                <thead class="bg-gradient-to-r from-green-600 to-green-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Tiket</th>
                        @if($isManager)
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Pelapor</th>
                        @endif
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Kendala</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Perangkat</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Lokasi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Prioritas</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase">Aksi</th>
                    </tr>
                </thead>

                <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600 dark:border-gray-700">
                    @forelse($reports as $report)
                        <tr class="hover:bg-green-50 dark:hover:bg-gray-600 transition duration-150">
                            <td class="px-6 py-4 text-sm font-mono font-semibold text-gray-900 dark:text-gray-100">{{ $report->ticket_number }}</td>
                            @if($isManager)
                            <td class="px-6 py-4 text-sm">
                                <p class="font-semibold uppercase text-gray-900 dark:text-gray-100">{{ $report->reporter->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $report->reporter->username ?? '-' }}</p>
                            </td>
                            @endif
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                <p class="font-semibold">{{ $report->title }}</p>
                                <p class="text-xs text-green-700 dark:text-green-500 mt-1">{{ $report->issue_category_label }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $report->created_at->format('d/m/Y H:i') }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm dark:text-gray-100">
                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $report->item->kategori ?? '-' }}</p>
                                <p class="text-xs font-mono text-gray-500 dark:text-gray-400">{{ $report->item->serial_number ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm uppercase text-gray-700 dark:text-gray-100">
                                {{ $report->location->gedung ?? '-' }} - {{ $report->location->ruangan ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $priorityClasses[$report->priority] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100' }}">
                                    {{ $report->priority_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClasses[$report->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100' }}">
                                    {{ $report->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('issue_reports.show', $report) }}" class="btn btn-primary btn-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isManager ? 8 : 7 }}" class="px-6 py-8 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
                                Belum ada laporan kendala.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white dark:bg-gray-700 px-4 py-4 border-t border-gray-200">
            {{ $reports->links() }}
        </div>
    </div>
</div>

@if(false && ! $isManager)
    <div id="userMaintenanceHistoryModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="flex min-h-screen w-full items-center justify-center p-4">
            <div class="w-full max-w-3xl rounded-lg bg-white p-6 shadow-2xl dark:bg-gray-700">
                <div class="mb-5 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-50">History Maintenance PC</h2>
                        <p id="userMaintenanceHistoryDevice" class="mt-1 text-sm uppercase text-gray-500 dark:text-gray-300"></p>
                    </div>
                    <button type="button" onclick="closeUserMaintenanceHistory()" class="btn btn-secondary btn-sm">Tutup</button>
                </div>

                <div id="userMaintenanceHistoryEmpty" class="hidden rounded-lg bg-yellow-100 p-4 text-sm font-semibold text-yellow-700">
                    PC ini belum punya history maintenance berkala.
                </div>

                <div id="userMaintenanceHistoryList" class="max-h-[65vh] space-y-3 overflow-y-auto"></div>
            </div>
        </div>
    </div>

<style>
/* =========================================
   LIGHT MODE
   ========================================= */
.select-kategori {
    color-scheme: light;
    background-color: #ffffff;
    color: #111827;
}
.select-kategori option {
    background-color: #ffffff;
    color: #111827;
}

/* =========================================
   DARK MODE
   ========================================= */
/* 1. Background Kotak Tombol Utama (#374151) */
.dark .select-kategori {
    color-scheme: dark !important; /* <--- KUNCI UTAMA UNTUK MS EDGE */
    background-color: #374151 !important;
    border-color: #4b4855 !important;
    color: #f9fafb !important;
}
/* 2. Background DAFTAR PILIHAN / DROPDOWN OPTION (#2d2b35) */
.dark .select-kategori option,
.dark .filter-option {
    background-color: #2d2b35 !important;
    color: #f9fafb !important;
}

/* 3. Highlight Opsi saat Di-hover / Dipilih (#403d49) */
.dark .filter-option:checked,
.dark .filter-option:hover,
.dark .select-kategori option:checked {
    background-color: #403d49 !important;
    color: #ffffff !important;
}
</style>

    <script>
    function escapeMaintenanceHtml(value)
    {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function openUserMaintenanceHistory(serialNumber, merk, histories)
    {
        const historyList = document.getElementById('userMaintenanceHistoryList');
        const historyEmpty = document.getElementById('userMaintenanceHistoryEmpty');

        document.getElementById('userMaintenanceHistoryDevice').textContent = serialNumber + ' / ' + merk;
        historyList.innerHTML = '';

        if (!histories.length) {
            historyEmpty.classList.remove('hidden');
        } else {
            historyEmpty.classList.add('hidden');

            histories.forEach(function (history) {
                const statusClass = history.status === 'Perlu Perbaikan'
                    ? 'bg-red-100 text-red-700'
                    : 'bg-green-100 text-green-700';
                const checklist = history.checklist.length
                    ? history.checklist.map(function (item) {
                        return `<span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-700">${escapeMaintenanceHtml(item)}</span>`;
                    }).join(' ')
                    : '<span class="text-sm text-gray-500">Checklist belum diisi</span>';

                historyList.insertAdjacentHTML('beforeend', `
                    <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-600">
                        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <div class="font-semibold text-gray-900 dark:text-gray-50">${escapeMaintenanceHtml(history.periode_bulan)}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-300">Tanggal cek: ${escapeMaintenanceHtml(history.tanggal_cek)}</div>
                            </div>
                            <span class="rounded px-2 py-1 text-xs font-semibold ${statusClass}">${escapeMaintenanceHtml(history.status)}</span>
                        </div>
                        <div class="grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
                            <div class="text-gray-700 dark:text-gray-100"><span class="font-semibold">Kondisi:</span> ${escapeMaintenanceHtml(history.kondisi)}</div>
                            <div class="text-gray-700 dark:text-gray-100"><span class="font-semibold">Teknisi:</span> ${escapeMaintenanceHtml(history.teknisi)}</div>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">${checklist}</div>
                        <div class="mt-3 text-sm text-gray-700 dark:text-gray-100"><span class="font-semibold">Catatan:</span> ${escapeMaintenanceHtml(history.catatan)}</div>
                    </div>
                `);
            });
        }

        document.getElementById('userMaintenanceHistoryModal').classList.remove('hidden');
        document.getElementById('userMaintenanceHistoryModal').classList.add('flex');
    }

    function closeUserMaintenanceHistory()
    {
        document.getElementById('userMaintenanceHistoryModal').classList.add('hidden');
        document.getElementById('userMaintenanceHistoryModal').classList.remove('flex');
    }
    </script>
@endif
@endsection
