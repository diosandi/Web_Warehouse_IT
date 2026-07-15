<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Warehouse IT</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; margin: 24px; font-size: 12px; }
        h1 { margin: 0 0 4px; font-size: 24px; }
        h2 { margin: 24px 0 10px; font-size: 16px; }
        p { margin: 4px 0; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 12px; page-break-inside: avoid; }
        th, td { border: 1px solid #d1d5db; padding: 6px; text-align: left; vertical-align: top; }
        th { background: #166534; color: #fff; }
        .summary { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin: 18px 0; }
        .card { border: 1px solid #d1d5db; border-left: 4px solid #16a34a; padding: 10px; }
        .label { color: #6b7280; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .value { font-size: 20px; font-weight: bold; margin-top: 4px; }
        .no-print { margin-bottom: 20px; }
        button { background: #16a34a; color: #fff; border: 0; border-radius: 6px; padding: 10px 14px; font-weight: bold; cursor: pointer; }
        @media print {
            body { margin: 12px; }
            .no-print { display: none; }
            .summary { grid-template-columns: repeat(4, 1fr); }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>

    <h1>Laporan Warehouse IT</h1>
    <p>Periode barang masuk: <strong>{{ $periodeLabel }}</strong></p>
    <p>Filter barang masuk: <strong>{{ $filterLabel }}</strong></p>
    <p>Tanggal export: {{ now()->format('d-m-Y H:i') }}</p>

    <div class="summary">
        <div class="card"><div class="label">Total Barang</div><div class="value">{{ number_format($summary['total_items']) }}</div></div>
        <div class="card"><div class="label">Tersedia</div><div class="value">{{ number_format($summary['available']) }}</div></div>
        <div class="card"><div class="label">Digunakan</div><div class="value">{{ number_format($summary['used']) }}</div></div>
        <div class="card"><div class="label">Pemeliharaan</div><div class="value">{{ number_format($summary['maintenance']) }}</div></div>
        <div class="card"><div class="label">Tidak Digunakan</div><div class="value">{{ number_format($summary['retired']) }}</div></div>
        <div class="card"><div class="label">Dibawa Vendor</div><div class="value">{{ number_format($summary['vendor']) }}</div></div>
        <div class="card"><div class="label">Distribusi Aktif</div><div class="value">{{ number_format($summary['active_distributions']) }}</div></div>
        <div class="card"><div class="label">Data Barang Masuk</div><div class="value">{{ number_format($summary['barang_masuk']) }}</div></div>
        <div class="card"><div class="label">Item Masuk Sesuai Periode</div><div class="value">{{ number_format($totalBarangMasukPeriode) }}</div></div>
    </div>

    <h2>Stok Per Kategori</h2>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Total</th>
                <th>Tersedia</th>
                <th>Digunakan</th>
                <th>Pemeliharaan</th>
                <th>Tidak Digunakan</th>
                <th>Vendor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stokPerKategori as $stok)
                <tr>
                    <td>{{ $stok['kategori'] }}</td>
                    <td>{{ number_format($stok['total']) }}</td>
                    <td>{{ number_format($stok['available']) }}</td>
                    <td>{{ number_format($stok['used']) }}</td>
                    <td>{{ number_format($stok['maintenance']) }}</td>
                    <td>{{ number_format($stok['retired']) }}</td>
                    <td>{{ number_format($stok['vendor']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Barang Masuk Per Kategori</h2>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Jumlah Masuk</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barangMasukPerKategori as $row)
                <tr>
                    <td>{{ $row['kategori'] }}</td>
                    <td>{{ number_format($row['jumlah']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Detail Barang Masuk</h2>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Asset</th>
                <th>No PO</th>
                <th>Kategori</th>
                <th>Merk</th>
                <th>Tipe</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($barangMasukDetail as $row)
                <tr>
                    <td>{{ \App\Support\DateFormatter::date($row['tanggal_masuk']) }}</td>
                    <td>{{ $row['asset'] }}</td>
                    <td>{{ $row['po_number'] }}</td>
                    <td>{{ $row['kategori'] }}</td>
                    <td>{{ $row['merk'] }}</td>
                    <td>{{ $row['type'] }}</td>
                    <td>{{ number_format($row['jumlah']) }}</td>
                    <td>{{ $row['keterangan'] }}</td>
                </tr>
            @empty
                <tr><td colspan="8">Tidak ada data barang masuk pada filter ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Distribusi Aktif Per Lokasi</h2>
    <table>
        <thead>
            <tr>
                <th>Gedung</th>
                <th>Ruangan</th>
                <th>PC</th>
                <th>Monitor</th>
                <th>Printer Kertas</th>
                <th>Printer Barcode</th>
                <th>Scanner</th>
                <th>Lainnya</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($distribusiPerLokasi as $lokasi)
                <tr>
                    <td>{{ $lokasi['gedung'] }}</td>
                    <td>{{ $lokasi['ruangan'] }}</td>
                    <td>{{ number_format($lokasi['PC']) }}</td>
                    <td>{{ number_format($lokasi['Monitor']) }}</td>
                    <td>{{ number_format($lokasi['Printer Kertas']) }}</td>
                    <td>{{ number_format($lokasi['Printer Barcode']) }}</td>
                    <td>{{ number_format($lokasi['Scanner']) }}</td>
                    <td>{{ number_format($lokasi['Lainnya']) }}</td>
                    <td>{{ number_format($lokasi['total']) }}</td>
                </tr>
            @empty
                <tr><td colspan="9">Belum ada distribusi aktif.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Perlu Perhatian</h2>
    <table>
        <thead>
            <tr>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($perluPerhatian as $warning)
                <tr>
                    <td>{{ $warning['title'] }}</td>
                    <td>{{ number_format($warning['count']) }}</td>
                    <td>{{ $warning['description'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Daftar Barang Pemeliharaan</h2>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Merk</th>
                <th>Tipe</th>
                <th>Serial Number</th>
                <th>Lokasi</th>
                <th>Keterangan Kondisi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($maintenanceItems as $item)
                <tr>
                    <td>{{ $item->kategori ?? '-' }}</td>
                    <td>{{ $item->merk ?? '-' }}</td>
                    <td>{{ $item->type ?? '-' }}</td>
                    <td>{{ $item->serial_number ?? '-' }}</td>
                    <td>{{ ($item->storageLocation->gedung ?? '-') . ' - ' . ($item->storageLocation->ruangan ?? '-') }}</td>
                    <td>{{ $item->condition_note ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="6">Tidak ada barang pemeliharaan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Barang Tanpa Detail Perangkat</h2>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Merk</th>
                <th>Tipe</th>
                <th>Serial Number</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($itemsTanpaDetail as $item)
                <tr>
                    <td>{{ $item->kategori ?? '-' }}</td>
                    <td>{{ $item->merk ?? '-' }}</td>
                    <td>{{ $item->type ?? '-' }}</td>
                    <td>{{ $item->serial_number ?? '-' }}</td>
                    <td>{{ $item->status ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Semua barang sudah memiliki detail perangkat.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
