<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        h2 { font-size: 15px; margin-top: 22px; margin-bottom: 8px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 14px; }
        th, td { border: 1px solid #777; padding: 6px; vertical-align: top; }
        th { background: #16a34a; color: #fff; font-weight: bold; }
        .section-title { background: #e5e7eb; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Laporan Warehouse IT</h1>
    <p>Periode barang masuk: {{ $periodeLabel }}</p>
    <p>Filter barang masuk: {{ $filterLabel }}</p>
    <p>Tanggal export: {{ now()->format('d-m-Y H:i') }}</p>

    <h2>Ringkasan Inventaris</h2>
    <table>
        <tr><td>Total Barang</td><td>{{ $summary['total_items'] }}</td></tr>
        <tr><td>Tersedia</td><td>{{ $summary['available'] }}</td></tr>
        <tr><td>Digunakan</td><td>{{ $summary['used'] }}</td></tr>
        <tr><td>Pemeliharaan</td><td>{{ $summary['maintenance'] }}</td></tr>
        <tr><td>Tidak Digunakan</td><td>{{ $summary['retired'] }}</td></tr>
        <tr><td>Dibawa Vendor</td><td>{{ $summary['vendor'] }}</td></tr>
        <tr><td>Distribusi Aktif</td><td>{{ $summary['active_distributions'] }}</td></tr>
        <tr><td>Data Barang Masuk</td><td>{{ $summary['barang_masuk'] }}</td></tr>
        <tr><td>Item Masuk Sesuai Periode</td><td>{{ $totalBarangMasukPeriode }}</td></tr>
    </table>

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
                    <td>{{ $stok['total'] }}</td>
                    <td>{{ $stok['available'] }}</td>
                    <td>{{ $stok['used'] }}</td>
                    <td>{{ $stok['maintenance'] }}</td>
                    <td>{{ $stok['retired'] }}</td>
                    <td>{{ $stok['vendor'] }}</td>
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
                    <td>{{ $row['jumlah'] }}</td>
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
                    <td>{{ $row['jumlah'] }}</td>
                    <td>{{ $row['keterangan'] }}</td>
                </tr>
            @empty
                <tr><td colspan="8">Tidak ada data barang masuk pada periode ini.</td></tr>
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
                    <td>{{ $lokasi['PC'] }}</td>
                    <td>{{ $lokasi['Monitor'] }}</td>
                    <td>{{ $lokasi['Printer Kertas'] }}</td>
                    <td>{{ $lokasi['Printer Barcode'] }}</td>
                    <td>{{ $lokasi['Scanner'] }}</td>
                    <td>{{ $lokasi['Lainnya'] }}</td>
                    <td>{{ $lokasi['total'] }}</td>
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
                    <td>{{ $warning['count'] }}</td>
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
