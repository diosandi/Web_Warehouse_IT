@extends('layouts.app')

@section('content')
<br>
<div class="mx-auto w-full px-3 py-8 sm:px-4 lg:px-6 lg:py-12">
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Maintenance PC Saya</h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Pantau status pengecekan berkala PC yang terhubung ke akun kamu.</p>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-3">
        <div class="rounded-xl border-l-4 border-green-500 bg-white p-6 shadow-lg dark:bg-gray-700">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">PC Aktif</p>
            <h3 class="mt-2 text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $distributionItems->count() }}</h3>
        </div>
        <div class="rounded-xl border-l-4 border-blue-500 bg-white p-6 shadow-lg dark:bg-gray-700">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sudah Dicek Bulan Ini</p>
            <h3 class="mt-2 text-3xl font-bold text-gray-800 dark:text-gray-100">
                {{ $distributionItems->filter(fn ($distributionItem) => $distributionItem->maintenanceBerkalas->firstWhere('periode_bulan', $periode))->count() }}
            </h3>
        </div>
        <div class="rounded-xl border-l-4 border-yellow-500 bg-white p-6 shadow-lg dark:bg-gray-700">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Periode</p>
            <h3 class="mt-2 text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $periode }}</h3>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-lg dark:bg-gray-700">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 bg-gray-50 px-4 py-4 dark:border-gray-600 dark:bg-gray-700 md:px-6">
            <div>
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Daftar PC</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Status maintenance berkala untuk bulan berjalan.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-green-600 to-green-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-white">PC</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-white">Lokasi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-white">Status Bulan Ini</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-white">Terakhir Dicek</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-white">Tracking</th>
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
                        <tr class="transition duration-150 hover:bg-green-50 dark:hover:bg-gray-800">
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
                                        <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800">Perlu Perbaikan</span>
                                    @else
                                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">Sudah Dicek</span>
                                    @endif
                                @else
                                    <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-800">Belum Dicek</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-100">
                                {{ $maintenanceTerakhir?->tanggal_cek ? \App\Support\DateFormatter::date($maintenanceTerakhir->tanggal_cek) : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-gray-700 dark:text-gray-100">{{ $distributionItem->maintenanceBerkalas->count() }} kali dicek</span>
                                    <button type="button"
                                        class="btn btn-secondary btn-sm"
                                        onclick="openMaintenanceHistory(@js($item?->serial_number ?? '-'), @js($item?->merk ?? '-'), @js($maintenanceHistory))">
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
</div>

<div id="maintenanceHistoryModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
    <div class="flex min-h-screen w-full items-center justify-center p-4">
        <div class="w-full max-w-3xl rounded-lg bg-white p-6 shadow-2xl dark:bg-gray-700">
            <div class="mb-5 flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-50">History Maintenance PC</h2>
                    <p id="maintenanceHistoryDevice" class="mt-1 text-sm uppercase text-gray-500 dark:text-gray-300"></p>
                </div>
                <button type="button" onclick="closeMaintenanceHistory()" class="btn btn-secondary btn-sm">Tutup</button>
            </div>

            <div id="maintenanceHistoryEmpty" class="hidden rounded-lg bg-yellow-100 p-4 text-sm font-semibold text-yellow-700">
                PC ini belum punya history maintenance berkala.
            </div>

            <div id="maintenanceHistoryList" class="max-h-[65vh] space-y-3 overflow-y-auto"></div>
        </div>
    </div>
</div>

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

function openMaintenanceHistory(serialNumber, merk, histories)
{
    const historyList = document.getElementById('maintenanceHistoryList');
    const historyEmpty = document.getElementById('maintenanceHistoryEmpty');

    document.getElementById('maintenanceHistoryDevice').textContent = serialNumber + ' / ' + merk;
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

    document.getElementById('maintenanceHistoryModal').classList.remove('hidden');
    document.getElementById('maintenanceHistoryModal').classList.add('flex');
}

function closeMaintenanceHistory()
{
    document.getElementById('maintenanceHistoryModal').classList.add('hidden');
    document.getElementById('maintenanceHistoryModal').classList.remove('flex');
}
</script>
@endsection
