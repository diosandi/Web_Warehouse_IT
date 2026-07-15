<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        p { margin: 2px 0; }
        table { border-collapse: collapse; width: 100%; margin-top: 14px; }
        th, td { border: 1px solid #777; padding: 6px; vertical-align: top; }
        th { background: #16a34a; color: #fff; font-weight: bold; }
        .text-center { text-align: center; }
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

    <h1>Laporan Barang Masuk</h1>
    <p>Periode: {{ $periodeLabel }}</p>
    <p>Filter: {{ $filterLabel }}</p>
    <p>Total transaksi barang masuk: {{ $barangMasuk->count() }}</p>
    <p>Total item / serial number: {{ $totalItem }}</p>
    <p>Tanggal export: {{ now()->format('d-m-Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Masuk</th>
                <th>Asset</th>
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
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ \App\Support\DateFormatter::date($barang->tanggal_masuk) }}</td>
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
                        <td>{{ \App\Support\DateFormatter::datetime($barang->created_at) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ \App\Support\DateFormatter::date($barang->tanggal_masuk) }}</td>
                        <td>{{ $barang->supplier ?? '-' }}</td>
                        <td>{{ $barang->po_number ?? '-' }}</td>
                        <td colspan="7">Tidak ada item terkait.</td>
                        <td>{{ $barang->keterangan ?? '-' }}</td>
                        <td>{{ \App\Support\DateFormatter::datetime($barang->created_at) }}</td>
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
