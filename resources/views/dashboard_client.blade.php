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
@endphp
<br>
<div class="distribution-page mx-auto w-full px-3 py-8 sm:px-4 lg:px-6 lg:py-12">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Pelaporan</h1>
        <p class="text-gray-600 mt-1">Perangkat dan laporan kendala untuk akun kamu.</p>
    </div>

    @php
        $assignedItemCount = $myDistributions->sum(function ($distribution) {
            return $distribution->distributionItems->where('status', 'dipakai')->count();
        });
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <p class="text-gray-500 text-sm font-medium">Perangkat Dipakai</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($assignedItemCount) }}</h3>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
            <p class="text-gray-500 text-sm font-medium">Laporan Baru</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($myReportSummary['open']) }}</h3>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-indigo-500">
            <p class="text-gray-500 text-sm font-medium">Sedang Diproses</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($myReportSummary['in_progress']) }}</h3>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <p class="text-gray-500 text-sm font-medium">Selesai</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($myReportSummary['resolved'] + $myReportSummary['closed']) }}</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
        <div class="xl:col-span-2 bg-white rounded-xl shadow-lg p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Perangkat Saya</h2>
                    <p class="text-sm text-gray-500 mt-1">Perangkat aktif yang ditugaskan ke akun kamu.</p>
                </div>
                <a href="{{ route('issue_reports.create') }}" class="btn btn-success btn-sm">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Laporan
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                            <th class="pb-3 font-semibold">Perangkat</th>
                            <th class="pb-3 font-semibold">Serial Number</th>
                            <th class="pb-3 font-semibold">Lokasi</th>
                            <th class="pb-3 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($myDistributions as $distribution)
                            @foreach($distribution->distributionItems->where('status', 'dipakai') as $distributionItem)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-4 pr-4">
                                        <p class="font-semibold text-gray-800">{{ $distributionItem->item->kategori ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">{{ $distributionItem->item->merk ?? '-' }} {{ $distributionItem->item->type ?? '' }}</p>
                                    </td>
                                    <td class="py-4 pr-4 font-mono text-gray-700">{{ $distributionItem->item->serial_number ?? '-' }}</td>
                                    <td class="py-4 pr-4 uppercase text-gray-700">
                                        {{ $distribution->location->gedung ?? '-' }} - {{ $distribution->location->ruangan ?? '-' }}
                                    </td>
                                    <td class="py-4">
                                        <a href="{{ route('issue_reports.create', ['distribution_item_id' => $distributionItem->id]) }}" class="btn btn-warning btn-sm">
                                            Laporkan
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-sm font-medium text-gray-500">
                                    Belum ada perangkat yang ditugaskan ke akun kamu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800">Aksi Cepat</h2>
                <p class="text-sm text-gray-500 mt-1">Kirim dan pantau laporan kendala.</p>
            </div>
            <div class="space-y-3">
                <a href="{{ route('issue_reports.create') }}" class="btn btn-success btn-block justify-between">
                    <span>Buat Laporan Kendala</span>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </a>
                <a href="{{ route('issue_reports.index') }}" class="btn btn-secondary btn-block justify-between">
                    <span>Riwayat Laporan</span>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Laporan Terakhir</h2>
                <p class="text-sm text-gray-500 mt-1">Status laporan kendala yang sudah kamu kirim.</p>
            </div>
            <a href="{{ route('issue_reports.index') }}" class="btn btn-primary btn-sm">Lihat Semua</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                        <th class="pb-3 font-semibold">No Tiket</th>
                        <th class="pb-3 font-semibold">Judul</th>
                        <th class="pb-3 font-semibold">Perangkat</th>
                        <th class="pb-3 font-semibold">Status</th>
                        <th class="pb-3 font-semibold">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($myReports as $report)
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 pr-4 font-mono font-semibold text-gray-800">
                                <a href="{{ route('issue_reports.show', $report) }}" class="hover:text-green-700">{{ $report->ticket_number }}</a>
                            </td>
                            <td class="py-4 pr-4 text-gray-800">{{ $report->title }}</td>
                            <td class="py-4 pr-4 text-gray-700">{{ $report->item->serial_number ?? '-' }}</td>
                            <td class="py-4 pr-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClasses[$report->status] ?? 'bg-gray-100 text-gray-800' }}">{{ $report->status_label }}</span>
                            </td>
                            <td class="py-4 text-gray-700">{{ $report->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-sm font-medium text-gray-500">
                                Belum ada laporan kendala.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-8 text-white">
        <h2 class="text-2xl font-bold mb-2">Selamat Datang, {{ Auth::user()->name }}!</h2>
        <p class="text-green-100">Laporkan kendala perangkat dari dashboard ini agar tim IT bisa menindaklanjuti.</p>
    </div>
</div>
@endsection
