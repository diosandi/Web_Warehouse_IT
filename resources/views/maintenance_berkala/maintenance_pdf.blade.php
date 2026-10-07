<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Maintenance Bulanan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        p { margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th, td { border: 1px solid #222; padding: 6px; text-align: left; vertical-align: top; }
        th { background: #e5e7eb; }
        @media print {
            @page { size: landscape; margin: 10mm; }
        }
    </style>
</head>
<body>
    <h1>Laporan Maintenance Bulanan</h1>
    <p>Periode: {{ $periode }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>PC</th>
                <th>Asset</th>
                <th>User / Divisi</th>
                <th>Lokasi</th>
                <th>Status</th>
                <th>Checklist</th>
                <th>Catatan</th>
                <th>Tanggal Cek</th>
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
                    <td>
                        {{ $item?->serial_number ?? '-' }}<br>
                        {{ trim(($item?->merk ?? '') . ' ' . ($item?->type ?? '')) ?: '-' }}
                    </td>
                    <td>{{ $item?->asset ?? '-' }}</td>
                    <td>
                        {{ $distribution?->user?->name ?? $distribution?->nama_user ?? '-' }}<br>
                        {{ $distribution?->divisi ?? '-' }}
                    </td>
                    <td>
                        {{ $distribution?->location?->gedung ?? '-' }} -
                        {{ $distribution?->location?->ruangan ?? '-' }}
                    </td>
                    <td>{{ $maintenance ? 'Sudah Dicek' : 'Belum Dicek' }}</td>
                    <td>{{ collect($maintenance ?->checklist ?? [])->filter()->implode(', ')?: '-' }}</td>
                    <td>{{ $maintenance ?->catatan ?? '-' }}</td>
                    <td>{{ $maintenance?->tanggal_cek?->format('d-m-Y') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
