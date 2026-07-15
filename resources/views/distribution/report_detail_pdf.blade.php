@php
    $categoryOptions = [
        'pc' => 'PC',
        'monitor' => 'Monitor',
        'printer_kertas' => 'Printer Kertas',
        'printer_barcode' => 'Printer Barcode',
        'scanner' => 'Scanner',
        'lainnya' => 'Lainnya',
    ];

    $selectedCategories = request()->has('kategori_laporan')
        ? (array) request('kategori_laporan')
        : array_keys($categoryOptions);

    $selectedAssets = array_values(array_filter((array) request('asset', [])));
    $showCategory = fn ($category) => in_array($category, $selectedCategories, true);
    $showAsset = fn ($item) => empty($selectedAssets) || in_array((string) ($item->asset ?? ''), $selectedAssets, true);
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Detail Distribusi</title>
    <style>
        @page { size: A4 landscape; margin: 8mm; }
        body { font-family: Arial, sans-serif; color: #111827; margin: 16px; }
        .actions { margin-bottom: 12px; }
        .actions button { background: #15803d; border: 0; border-radius: 6px; color: white; cursor: pointer; font-weight: 700; padding: 8px 12px; }
        h1 { font-size: 18px; margin: 0; }
        p { font-size: 11px; margin: 4px 0 12px; }
        table { border-collapse: collapse; width: 100%; font-size: 8px; }
        th { background: #15803d; color: white; }
        th, td { border: 1px solid #9ca3af; padding: 4px; text-align: left; vertical-align: top; }
        .nowrap { white-space: nowrap; }
        .muted { color: #4b5563; font-size: 7px; font-weight: 700; }
        @media print {
            body { margin: 0; }
            .actions { display: none; }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>

    <h1>Laporan Detail Distribusi</h1>
    <p>Dicetak: {{ now()->format('d-m-Y H:i') }} | Total: {{ $reports->count() }} data</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama User</th>
                <th>Divisi</th>
                <th>Gedung</th>
                <th>Ruangan</th>

                @if($showCategory('pc'))
                    <th>PC</th>
                    <th>SN PC / Asset</th>
                    <th>PC Name</th>
                    <th>User Account</th>
                    <th>IP</th>
                    <th>MAC LAN</th>
                    <th>OS</th>
                    <th>Office</th>
                @endif

                @if($showCategory('monitor'))
                    <th>Monitor</th>
                    <th>SN Monitor / Asset</th>
                @endif

                @if($showCategory('printer_kertas'))
                    <th>Printer Kertas</th>
                    <th>SN Printer / Asset</th>
                    <th>Detail Printer</th>
                @endif

                @if($showCategory('printer_barcode'))
                    <th>Printer Barcode</th>
                    <th>SN Barcode / Asset</th>
                    <th>Detail Barcode</th>
                @endif

                @if($showCategory('scanner'))
                    <th>Scanner</th>
                    <th>SN Scanner / Asset</th>
                @endif

                @if($showCategory('lainnya'))
                    <th>Lainnya</th>
                    <th>SN Lainnya / Asset</th>
                @endif

                <th>Tanggal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $distribution)
                @php
                    $visibleItems = ($distribution->status === 'dikembalikan'
                            ? $distribution->distributionItems
                            : $distribution->distributionItems->where('status', 'dipakai'))
                        ->map(fn ($distributionItem) => $distributionItem->item)
                        ->filter()
                        ->filter($showAsset);

                    $pc = $visibleItems->firstWhere('kategori', 'PC');
                    $pcDetail = $pc?->device_detail;
                    $monitors = $visibleItems->where('kategori', 'Monitor');
                    $printerKertas = $visibleItems->where('kategori', 'Printer Kertas');
                    $printerBarcode = $visibleItems->where('kategori', 'Printer Barcode');
                    $scanners = $visibleItems->where('kategori', 'Scanner');
                    $lainnya = $visibleItems->where('kategori', 'Lainnya');

                    $formatItem = fn ($items) => $items->map(fn ($item) => trim(($item->merk ?? '-') . ' / ' . ($item->type ?? '-')))->filter()->implode(', ');
                    $formatSnWithAsset = function ($items) {
                        return $items->map(function ($item) {
                            return e($item->serial_number ?: '-') .
                                '<br><span class="muted">Asset: ' .
                                e($item->asset ?: '-') .
                                '</span>';
                        })->filter()->implode('<hr>');
                    };
                    $formatSingleSnWithAsset = fn ($item) => $item ? $formatSnWithAsset(collect([$item])) : '-';
                    $formatPrinterDetail = function ($items) {
                        return $items->map(function ($item) {
                            $detail = $item?->device_detail;
                            return 'Koneksi: ' . ($detail?->connection_type ?? '-') .
                                '<br>IP: ' . ($detail?->ip_address ?? '-') .
                                '<br>Shared: ' . ($detail?->shared_name ?? '-');
                        })->implode('<hr>');
                    };
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $distribution->nama_user ?? '-' }}</td>
                    <td>{{ $distribution->divisi ?? '-' }}</td>
                    <td>{{ $distribution->location->gedung ?? '-' }}</td>
                    <td>{{ $distribution->location->ruangan ?? '-' }}</td>

                    @if($showCategory('pc'))
                        <td>{{ $pc ? trim(($pc->merk ?? '-') . ' / ' . ($pc->type ?? '-')) : '-' }}</td>
                        <td>{!! $formatSingleSnWithAsset($pc) !!}</td>
                        <td>{{ $pcDetail->pc_name ?? '-' }}</td>
                        <td>{{ $pcDetail->user_account ?? '-' }}</td>
                        <td>{{ $pcDetail->ip_address ?? '-' }}</td>
                        <td>{{ $pcDetail->mac_lan ?? '-' }}</td>
                        <td>{{ $pc->os ?? '-' }}</td>
                        <td>{{ $pcDetail->office_version ?? '-' }}</td>
                    @endif

                    @if($showCategory('monitor'))
                        <td>{{ $formatItem($monitors) ?: '-' }}</td>
                        <td>{!! $monitors->count() ? $formatSnWithAsset($monitors) : '-' !!}</td>
                    @endif

                    @if($showCategory('printer_kertas'))
                        <td>{{ $formatItem($printerKertas) ?: '-' }}</td>
                        <td>{!! $printerKertas->count() ? $formatSnWithAsset($printerKertas) : '-' !!}</td>
                        <td>{!! $printerKertas->count() ? $formatPrinterDetail($printerKertas) : '-' !!}</td>
                    @endif

                    @if($showCategory('printer_barcode'))
                        <td>{{ $formatItem($printerBarcode) ?: '-' }}</td>
                        <td>{!! $printerBarcode->count() ? $formatSnWithAsset($printerBarcode) : '-' !!}</td>
                        <td>{!! $printerBarcode->count() ? $formatPrinterDetail($printerBarcode) : '-' !!}</td>
                    @endif

                    @if($showCategory('scanner'))
                        <td>{{ $formatItem($scanners) ?: '-' }}</td>
                        <td>{!! $scanners->count() ? $formatSnWithAsset($scanners) : '-' !!}</td>
                    @endif

                    @if($showCategory('lainnya'))
                        <td>{{ $formatItem($lainnya) ?: '-' }}</td>
                        <td>{!! $lainnya->count() ? $formatSnWithAsset($lainnya) : '-' !!}</td>
                    @endif

                    <td class="nowrap">{{ \App\Support\DateFormatter::date($distribution->tanggal_distribusi) }}</td>
                    <td>{{ $distribution->status === 'dipakai' ? 'Dipakai' : 'Dikembalikan' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="40">Tidak ada data laporan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>
