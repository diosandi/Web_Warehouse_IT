@extends('layouts.app')

@section('content')
@php
    $showRedirect = route('items.show', [$item->id, 'redirect' => $redirect]);
    $activeDistributionCount = $activeDistributions->count();
    $isCurrentlyUsed = $activeDistributionCount > 0;
    $displayStatus = $isCurrentlyUsed
        ? 'used'
        : ($item->status === 'used' ? 'available' : $item->status);
    $isMaintenance = $displayStatus === 'maintenance';
    $statusLabel = [
        'available' => 'Tersedia',
        'used' => 'Dipakai',
        'maintenance' => 'Pemeliharaan',
        'retired' => 'Tidak Digunakan',
        'vendor' => 'Dibawa Vendor',
    ][$displayStatus] ?? ucfirst($displayStatus ?? '-');
    $statusClass = [
        'available' => 'bg-green-100 text-green-700 border-green-200',
        'used' => 'bg-blue-100 text-blue-700 border-blue-200',
        'maintenance' => 'bg-red-100 text-red-700 border-red-200',
        'retired' => 'bg-gray-100 text-gray-700 border-gray-200',
        'vendor' => 'bg-purple-100 text-purple-700 border-purple-200',
    ][$displayStatus] ?? 'bg-gray-100 text-gray-700 border-gray-200';
    $activeLocations = $activeDistributions
        ->map(fn ($distributionItem) => $distributionItem->distribution?->location)
        ->filter()
        ->unique('id')
        ->map(fn ($location) => ($location->gedung ?? '-') . ' - ' . ($location->ruangan ?? '-'))
        ->values();
    $currentLocation = $isCurrentlyUsed
        ? ($activeLocations->isNotEmpty() ? $activeLocations->implode(', ') : '-')
        : (($item->storageLocation->gedung ?? '-') . ' - ' . ($item->storageLocation->ruangan ?? '-'));
    $detail = $item->device_detail;
@endphp

<div class="container mx-auto px-4 py-8 md:py-10">
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase text-green-700">Master Data Barang</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 md:text-3xl">{{ $item->serial_number }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $item->kategori }} / {{ $item->merk ?? '-' }} / {{ $item->type ?? '-' }}</p>
        </div>

        <a href="{{ $redirect }}" class="btn btn-secondary btn-sm">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg border-l-4 border-green-500 bg-green-50 p-4 text-sm font-medium text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-lg border-l-4 border-red-500 bg-red-50 p-4 text-sm font-medium text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <section class="mb-6 rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="grid flex-1 grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-500">Status</p>
                    <span class="mt-2 inline-flex rounded-full border px-3 py-1 text-sm font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-500">Asset/Kepemilikan</p>
                    <p class="mt-2 text-sm font-semibold uppercase text-gray-900">{{ $item->asset ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-500">{{ $isCurrentlyUsed ? 'Lokasi Pemakaian' : 'Lokasi Penyimpanan' }}</p>
                    <p class="mt-2 text-sm font-semibold uppercase text-gray-900">{{ $currentLocation }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-500">Pengguna Aktif</p>
                    <p class="mt-2 text-sm font-semibold uppercase text-gray-900">
                        {{ $activeDistributionCount ? $activeDistributionCount . ' user' : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-500">Catatan Kondisi</p>
                    <p class="mt-2 text-sm text-gray-900">{{ $item->condition_note ?? '-' }}</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 lg:justify-end">
                <a href="{{ route('items.edit', [$item->id, 'redirect' => $showRedirect]) }}"
                    class="btn btn-primary btn-sm">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Barang
                </a>

                @if($activeDistributionItem)
                    <button type="button"
                        onclick="openReturnModal({{ $activeDistributionItem->id }})"
                        class="btn btn-danger btn-sm">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6M20 9A8 8 0 006.7 4.7L4 10M4 15a8 8 0 0013.3 4.3L20 14"></path>
                        </svg>
                        Kembalikan Barang
                    </button>
                @endif
            </div>
        </div>
    </section>

    <section id="device-detail" class="mb-6 rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:p-6">
        <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Detail Perangkat</h2>
                <p class="text-sm text-gray-600">Informasi teknis untuk pengecekan jaringan dan perangkat.</p>
            </div>

            @if($detail)
                <a href="{{ route('device_details.edit', [$detail->id, 'redirect' => $showRedirect]) }}"
                    class="btn btn-success btn-sm">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Detail Perangkat
                </a>
            @else
                <a href="{{ route('device_details.create', ['item_id' => $item->id, 'redirect' => $showRedirect]) }}"
                    class="btn btn-primary btn-sm">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Detail Perangkat
                </a>
            @endif
        </div>

        @if($detail)
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                @foreach([
                    'IP Address' => $detail->ip_address,
                    'Nama PC' => $detail->pc_name,
                    'Nama Sharing' => $detail->shared_name,
                    'Port' => $detail->port,
                    'Jenis Koneksi' => $detail->connection_type,
                    'Akun Pengguna' => $detail->user_account,
                    'MAC LAN' => $detail->mac_lan,
                    'MAC WIFI' => $detail->mac_wifi,
                    'Versi OS' => $detail->os_version,
                    'Versi Office' => $detail->office_version,
                ] as $label => $value)
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                        <p class="text-xs font-semibold uppercase text-gray-500">{{ $label }}</p>
                        <p class="mt-1 break-words text-sm font-semibold text-gray-900">{{ $value ?? '-' }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-3">
                <p class="text-xs font-semibold uppercase text-gray-500">Catatan Perangkat</p>
                <p class="mt-1 text-sm text-gray-900">{{ $detail->catatan ?? '-' }}</p>
            </div>
        @else
            <div class="rounded-lg border-l-4 border-yellow-500 bg-yellow-50 p-4 text-sm font-medium text-yellow-700">
                Detail Perangkat belum tersedia.
            </div>
        @endif
    </section>

    <section id="distribusi-aktif" class="mb-6 rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:p-6">
        <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Distribusi Aktif</h2>
                <p class="text-sm text-gray-600">Daftar pengguna dan lokasi yang sedang memakai item ini.</p>
            </div>
            <a href="{{ route('distribution.index', ['item_id' => $item->id]) }}"
                class="btn btn-warning btn-sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6m-6 4h6m-6 4h4m6-8v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2h6l6 6z"></path>
                </svg>
                Lihat Distribusi
            </a>
        </div>

        @if($activeDistributions->count())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] divide-y divide-gray-200">
                    <thead class="bg-green-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">Pengguna</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">Divisi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">Lokasi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($activeDistributions as $distributionItem)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-semibold uppercase text-gray-900">{{ $distributionItem->distribution->nama_user ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm uppercase text-gray-700">{{ $distributionItem->distribution->divisi ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm uppercase text-gray-700">
                                    {{ $distributionItem->distribution->location->gedung ?? '-' }}
                                    -
                                    {{ $distributionItem->distribution->location->ruangan ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ \App\Support\DateFormatter::date($distributionItem->distribution->tanggal_distribusi ?? null) }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">Dipakai</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-lg border-l-4 border-green-500 bg-green-50 p-4 text-sm font-medium text-green-700">
                Barang sedang tidak digunakan.
            </div>
        @endif
    </section>

    <section id="history-status-barang" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:p-6">
        <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Riwayat Status Barang</h2>
                <p class="text-sm text-gray-600">Timeline distribusi, pengembalian, dan perubahan status dari master barang.</p>
                <p class="mt-1 text-xs font-semibold uppercase text-gray-500">Total: {{ $statusHistoryEvents->total() }} riwayat</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('items.history.export', array_merge([$item->id, 'excel'], request()->query())) }}"
                    class="btn btn-success btn-sm">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m4 5H5a2 2 0 01-2-2V6a2 2 0 012-2h8l6 6v8a2 2 0 01-2 2z"></path>
                    </svg>
                    Ekspor Excel
                </a>
                <a href="{{ route('items.history.export', array_merge([$item->id, 'pdf'], request()->query())) }}"
                    target="_blank"
                    class="btn btn-danger btn-sm">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m4 5H5a2 2 0 01-2-2V6a2 2 0 012-2h8l6 6v8a2 2 0 01-2 2z"></path>
                    </svg>
                    Ekspor PDF
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('items.show', $item->id) }}" class="mb-5 rounded-lg border border-gray-200 bg-gray-50 p-4">
            <input type="hidden" name="redirect" value="{{ $redirect }}">

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Tanggal Dari</label>
                    <input type="date" name="history_date_from" value="{{ $statusHistoryFilters['history_date_from'] }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Tanggal Sampai</label>
                    <input type="date" name="history_date_to" value="{{ $statusHistoryFilters['history_date_to'] }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Jenis Riwayat</label>
                    <select name="history_event_type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">Semua</option>
                        <option value="distribution" {{ $statusHistoryFilters['history_event_type'] === 'distribution' ? 'selected' : '' }}>Distribusi</option>
                        <option value="return" {{ $statusHistoryFilters['history_event_type'] === 'return' ? 'selected' : '' }}>Pengembalian</option>
                        <option value="manual" {{ $statusHistoryFilters['history_event_type'] === 'manual' ? 'selected' : '' }}>Edit Master Barang</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Baris</label>
                    <select name="history_per_page" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        @foreach([10, 20, 50, 100] as $perPage)
                            <option value="{{ $perPage }}" {{ $statusHistoryFilters['history_per_page'] === $perPage ? 'selected' : '' }}>{{ $perPage }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <button type="submit" class="btn btn-success btn-sm">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707L14 14v4l-4 2v-6L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Terapkan Filter
                </button>
                <a href="{{ route('items.show', [$item->id, 'redirect' => $redirect]) }}"
                    class="btn btn-secondary btn-sm">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Bersihkan
                </a>
            </div>
        </form>

        @if($statusHistoryEvents->count())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[920px] divide-y divide-gray-200">
                    <thead class="bg-green-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">Waktu</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">Aktivitas</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">Perubahan Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">Pengguna/Oleh</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">Lokasi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($statusHistoryEvents as $event)
                            @php
                                $eventClass = [
                                    'distribution' => 'bg-blue-100 text-blue-700',
                                    'return' => 'bg-green-100 text-green-700',
                                    'manual' => 'bg-purple-100 text-purple-700',
                                ][$event['event_type']] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ \App\Support\DateFormatter::datetime($event['display_at']) }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $eventClass }}">
                                        {{ $event['event_label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                            {{ $event['old_status_label'] }}
                                        </span>
                                        <span class="text-xs font-bold text-gray-400">&rarr;</span>
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $event['new_status_class'] }}">
                                            {{ $event['new_status_label'] }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <p class="text-xs font-semibold uppercase text-gray-500">{{ $event['actor_label'] }}</p>
                                    <p class="font-semibold uppercase text-gray-900">{{ $event['actor'] }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm uppercase text-gray-700">
                                    {{ $event['location'] }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $event['note'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $statusHistoryEvents->links() }}
            </div>
        @else
            <div class="rounded-lg border-l-4 border-yellow-500 bg-yellow-50 p-4 text-sm font-medium text-yellow-700">
                Belum ada riwayat status untuk item ini.
            </div>
        @endif
    </section>
</div>

<div id="returnModal" class="fixed inset-0 z-50 hidden bg-black/50">
    <div class="flex min-h-screen w-full items-center justify-center p-4">
        <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-2xl">
            <h2 class="mb-6 text-xl font-bold text-gray-900">Pengembalian Barang</h2>

            <form id="returnForm" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Lokasi Penyimpanan</label>
                    <select name="storage_location_id" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm" required>
                        <option class="text-sm uppercase" value="">-- Pilih Lokasi --</option>
                        @foreach($locations as $location)
                            <option class="text-sm uppercase" value="{{ $location->id }}">{{ $location->gedung }} - {{ $location->ruangan }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Kondisi Barang</label>
                    <select name="condition_status" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm">
                        <option value="available">Normal / Tersedia</option>
                        <option value="maintenance">Rusak / Pemeliharaan</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Keterangan Kondisi</label>
                    <textarea name="condition_note" rows="4" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm" placeholder="Contoh: monitor bergaris, printer mati total"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeReturnModal()" class="btn btn-secondary btn-sm">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batal
                    </button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openReturnModal(distributionItemId)
{
    let form = document.getElementById('returnForm');
    form.action = `/distribution-item/${distributionItemId}/return`;

    let modal = document.getElementById('returnModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeReturnModal()
{
    let modal = document.getElementById('returnModal');
    modal.classList.remove('flex');
    modal.classList.add('hidden');
}
</script>
@endsection
