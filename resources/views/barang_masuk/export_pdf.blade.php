<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Barang Masuk</title>
    <style>
        @page { size: landscape; margin: 12mm; }
        body { font-family: Arial, sans-serif; color: #111827; margin: 24px; font-size: 12px; }
        h1 { margin: 0 0 4px; font-size: 24px; }
        p { margin: 4px 0; }
        table { border-collapse: collapse; width: 100%; margin-top: 14px; }
        th, td { border: 1px solid #d1d5db; padding: 6px; text-align: left; vertical-align: top; }
        th { background: #166534; color: #fff; }
        .summary { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin: 18px 0; }
        .card { border: 1px solid #d1d5db; border-left: 4px solid #16a34a; padding: 10px; }
        .label { color: #6b7280; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .value { font-size: 18px; font-weight: bold; margin-top: 4px; }
        .no-print { margin-bottom: 20px; }
        button { background: #16a34a; color: #fff; border: 0; border-radius: 6px; padding: 10px 14px; font-weight: bold; cursor: pointer; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    @php
        $no = 1;
        $totalItem = $barangMasuk->sum(fn ($barang) => $barang->items->count());
        $statusLabels = [
            'available' => 'Tersedia',
            'used' => 'Digunakan',
            'maintenance' => 'Pemeliharaan',
            'retired' => 'Tidak Digunakan',
        ];
    @endphp

    <div class="no-print">
        <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>

    <h1>Laporan Barang Masuk</h1>
    <p>Periode: <strong>{{ $periodeLabel }}</strong></p>
    <p>Filter: <strong>{{ $filterLabel }}</strong></p>
    <p>Tanggal export: {{ now()->format('d-m-Y H:i') }}</p>

    <div class="summary">
        <div class="card">
            <div class="label">Transaksi Barang Masuk</div>
            <div class="value">{{ number_format($barangMasuk->count()) }}</div>
        </div>
        <div class="card">
            <div class="label">Item / Serial Number</div>
            <div class="value">{{ number_format($totalItem) }}</div>
        </div>
        <div class="card">
            <div class="label">Periode</div>
            <div class="value">{{ $periodeLabel }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Masuk</th>
                <th>Supplier</th>
                <th>No PO</th>
                <th>Kategori</th>
                <th>Merk</th>
                <th>Tipe / Series</th>
                <th>Serial Number</th>
                <th>Service Tag</th>
                <th>Status</th>
                <th>Lokasi Penyimpanan</th>
                <th>Keterangan</th>
                <th>Tanggal Input</th>
            </tr>
        </thead>
        <tbody>
            @forelse($barangMasuk as $barang)
                @forelse($barang->items as $item)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $barang->tanggal_masuk ?? '-' }}</td>
                        <td>{{ $barang->supplier ?? '-' }}</td>
                        <td>{{ $barang->po_number ?? '-' }}</td>
                        <td>{{ $item->kategori ?? '-' }}</td>
                        <td>{{ $item->merk ?? '-' }}</td>
                        <td>{{ $item->type ?? '-' }}</td>
                        <td>{{ $item->serial_number ?? '-' }}</td>
                        <td>{{ $item->service_tag ?? '-' }}</td>
                        <td>{{ $statusLabels[$item->status] ?? ($item->status ?? '-') }}</td>
                        <td>{{ ($item->storageLocation->gedung ?? '-') . ' - ' . ($item->storageLocation->ruangan ?? '-') }}</td>
                        <td>{{ $barang->keterangan ?? '-' }}</td>
                        <td>{{ $barang->created_at ? $barang->created_at->format('d-m-Y H:i') : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $barang->tanggal_masuk ?? '-' }}</td>
                        <td>{{ $barang->supplier ?? '-' }}</td>
                        <td>{{ $barang->po_number ?? '-' }}</td>
                        <td colspan="7">Tidak ada item terkait.</td>
                        <td>{{ $barang->keterangan ?? '-' }}</td>
                        <td>{{ $barang->created_at ? $barang->created_at->format('d-m-Y H:i') : '-' }}</td>
                    </tr>
                @endforelse
            @empty
                <tr>
                    <td colspan="13">Tidak ada data barang masuk.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
