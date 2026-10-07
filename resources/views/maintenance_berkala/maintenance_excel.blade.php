<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th colspan="11">Laporan Maintenance Bulanan - {{ $periode }}</th>
            </tr>
            <tr>
                <th>No</th>
                <th>Serial Number</th>
                <th>Merk / Tipe</th>
                <th>Asset</th>
                <th>User</th>
                <th>Divisi</th>
                <th>Lokasi</th>
                <th>Status</th>
                <th>Checklist</th>
                <th>Catatan</th>
                <th>Tanggal Dicek</th>
            </tr>
        </thead>
        <tbody>
            @forelse($distributionItems as $index => $distributionItem)
                @php
                    $item = $distributionItem->item;
                    $distribution = $distributionItem->distribution;
                    $maintenance = $distributionItem->maintenanceBerkalas
                        ->firstWhere('periode_bulan', $periode);
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item?->serial_number ?? '-' }}</td>
                    <td>{{ trim(($item?->merk ?? '') . ' ' . ($item?->type ?? '')) ?: '-' }}</td>
                    <td>{{ $item?->asset ?? '-' }}</td>
                    <td>{{ $distribution?->user?->name ?? $distribution?->nama_user ?? '-' }}</td>
                    <td>{{ $distribution?->divisi ?? '-' }}</td>
                    <td>
                        {{ $distribution?->location?->gedung ?? '-' }}
                        -
                        {{ $distribution?->location?->ruangan ?? '-' }}
                    </td>
                    <td>{{ $maintenance ? 'Sudah Dicek' : 'Belum Dicek' }}</td>
                    <td>{{ collect($maintenance ?->checklist ?? [])->filter()->implode(', ')?: '-' }}</td>
                    <td>{{ $maintenance ?->catatan ?? '-' }}</td>
                    <td>{{ $maintenance?->tanggal_cek?->format('d-m-Y') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
