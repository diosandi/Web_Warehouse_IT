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
            <h1 class="text-3xl font-bold text-gray-800">{{ $isManager ? 'Antrian Laporan Kendala' : 'Laporan Kendala Saya' }}</h1>
            <p class="text-gray-600 mt-1">{{ $isManager ? 'Pantau dan tindak lanjuti laporan dari user.' : 'Pantau laporan kendala perangkat yang kamu kirim.' }}</p>
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
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
            <p class="text-gray-500 text-sm font-medium">Baru</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($summary['open']) }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <p class="text-gray-500 text-sm font-medium">Diproses</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($summary['in_progress']) }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <p class="text-gray-500 text-sm font-medium">Selesai</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($summary['resolved']) }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-gray-500">
            <p class="text-gray-500 text-sm font-medium">Ditutup</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($summary['closed']) }}</h3>
        </div>
    </div>

    <details class="bg-white rounded-xl shadow-lg mb-6 group" {{ $hasActiveFilter ? 'open' : '' }}>
        <summary class="list-none p-4 md:p-6 cursor-pointer flex items-center justify-between gap-3">
            <h2 class="text-lg md:text-xl font-bold text-gray-800">Filter Laporan</h2>
            <span class="text-sm text-gray-500">Pilih</span>
        </summary>

        <div class="px-4 md:px-6 pb-4 md:pb-6">
            <form method="GET" action="{{ route('issue_reports.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                    <div>
                        <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Cari</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tiket, judul, SN, pelapor"
                            class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                        <select name="issue_category" class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Semua</option>
                            @foreach($categoryOptions as $category => $label)
                                <option value="{{ $category }}" {{ request('issue_category') === $category ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Semua</option>
                            @foreach($statusOptions as $status => $label)
                                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Prioritas</label>
                        <select name="priority" class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Semua</option>
                            @foreach($priorityOptions as $priority => $label)
                                <option value="{{ $priority }}" {{ request('priority') === $priority ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-success">Cari</button>
                    <a href="{{ route('issue_reports.index') }}" class="btn btn-secondary">Bersihkan</a>
                </div>
            </form>
        </div>
    </details>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-4 md:px-6 py-3 md:py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center flex-wrap gap-2">
            <div class="text-xs md:text-sm text-gray-600">
                <span class="font-semibold text-gray-800">{{ $reports->total() }}</span>
                <span>Laporan Ditemukan</span>
            </div>
            <div class="text-xs md:text-sm text-gray-600">
                Halaman <span class="font-semibold">{{ $reports->currentPage() }}</span> dari <span class="font-semibold">{{ $reports->lastPage() }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
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

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reports as $report)
                        <tr class="hover:bg-green-50 transition duration-150">
                            <td class="px-6 py-4 text-sm font-mono font-semibold text-gray-900">{{ $report->ticket_number }}</td>
                            @if($isManager)
                                <td class="px-6 py-4 text-sm">
                                    <p class="font-semibold uppercase text-gray-900">{{ $report->reporter->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">{{ $report->reporter->username ?? '-' }}</p>
                                </td>
                            @endif
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <p class="font-semibold">{{ $report->title }}</p>
                                <p class="text-xs text-green-700 mt-1">{{ $report->issue_category_label }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $report->created_at->format('d/m/Y H:i') }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <p class="font-semibold text-gray-900">{{ $report->item->kategori ?? '-' }}</p>
                                <p class="text-xs font-mono text-gray-500">{{ $report->item->serial_number ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm uppercase text-gray-700">
                                {{ $report->location->gedung ?? '-' }} - {{ $report->location->ruangan ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $priorityClasses[$report->priority] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $report->priority_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClasses[$report->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $report->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('issue_reports.show', $report) }}" class="btn btn-primary btn-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isManager ? 8 : 7 }}" class="px-6 py-8 text-center text-sm font-medium text-gray-500">
                                Belum ada laporan kendala.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white px-4 py-4 border-t border-gray-200">
            {{ $reports->links() }}
        </div>
    </div>
</div>
@endsection
