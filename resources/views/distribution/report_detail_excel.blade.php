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
<html>
<head>
    <meta charset="utf-8">
    <style>
        table { border-collapse: collapse; font-family: Calibri, Arial, sans-serif; font-size: 11px; }
        th { background: #15803d; color: #ffffff; font-weight: 700; }
        th, td { border: 1px solid #111827; padding: 5px; vertical-align: top; white-space: nowrap; }
        .wrap { white-space: pre-line; }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th colspan="40">Laporan Detail Distribusi</th>
            </tr>
            <tr>
                <th>No</th>
                <th>Nama User</th>
                <th>Divisi</th>
                <th>Gedung</th>
                <th>Ruangan</th>

                @if($showCategory('pc'))
                    <th>PC</th>
                    <th>SN PC / Asset</th>
                    <th>Service Tag</th>
                    <th>PC Name</th>
                    <th>User Account</th>
                    <th>IP</th>
                    <th>MAC LAN</th>
                    <th>Processor</th>
                    <th>RAM</th>
                    <th>Storage</th>
                    <th>VGA</th>
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
                    <th>SN Printer Barcode / Asset</th>
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

                <th>Tanggal Distribusi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $distribution)
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
                    $formatSnWithAsset = fn ($items) => $items->map(fn ($item) => ($item->serial_number ?: '-') . "\nAsset: " . ($item->asset ?: '-'))->filter()->implode("\n---\n");
                    $formatSingleSnWithAsset = fn ($item) => $item ? (($item->serial_number ?: '-') . "\nAsset: " . ($item->asset ?: '-')) : '-';
                    $formatPrinterDetail = function ($items) {
                        return $items->map(function ($item) {
                            $detail = $item?->device_detail;
                            return 'Koneksi: ' . ($detail?->connection_type ?? '-') .
                                "\nIP: " . ($detail?->ip_address ?? '-') .
                                "\nShared: " . ($detail?->shared_name ?? '-');
                        })->implode("\n---\n");
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
                        <td class="wrap">{{ $formatSingleSnWithAsset($pc) }}</td>
                        <td>{{ $pc->service_tag ?? '-' }}</td>
                        <td>{{ $pcDetail->pc_name ?? '-' }}</td>
                        <td>{{ $pcDetail->user_account ?? '-' }}</td>
                        <td>{{ $pcDetail->ip_address ?? '-' }}</td>
                        <td>{{ $pcDetail->mac_lan ?? '-' }}</td>
                        <td>{{ $pc->processor ?? '-' }}</td>
                        <td>{{ $pc?->ram_gb ? $pc->ram_gb . ' GB' : '-' }}</td>
                        <td>{{ $pc?->storage_gb ? $pc->storage_gb . ' GB' : '-' }}</td>
                        <td>{{ $pc->vga ?? '-' }}</td>
                        <td>{{ $pc->os ?? '-' }}</td>
                        <td>{{ $pcDetail->office_version ?? '-' }}</td>
                    @endif

                    @if($showCategory('monitor'))
                        <td>{{ $formatItem($monitors) ?: '-' }}</td>
                        <td class="wrap">{{ $formatSnWithAsset($monitors) ?: '-' }}</td>
                    @endif

                    @if($showCategory('printer_kertas'))
                        <td>{{ $formatItem($printerKertas) ?: '-' }}</td>
                        <td class="wrap">{{ $formatSnWithAsset($printerKertas) ?: '-' }}</td>
                        <td class="wrap">{{ $printerKertas->count() ? $formatPrinterDetail($printerKertas) : '-' }}</td>
                    @endif

                    @if($showCategory('printer_barcode'))
                        <td>{{ $formatItem($printerBarcode) ?: '-' }}</td>
                        <td class="wrap">{{ $formatSnWithAsset($printerBarcode) ?: '-' }}</td>
                        <td class="wrap">{{ $printerBarcode->count() ? $formatPrinterDetail($printerBarcode) : '-' }}</td>
                    @endif

                    @if($showCategory('scanner'))
                        <td>{{ $formatItem($scanners) ?: '-' }}</td>
                        <td class="wrap">{{ $formatSnWithAsset($scanners) ?: '-' }}</td>
                    @endif

                    @if($showCategory('lainnya'))
                        <td>{{ $formatItem($lainnya) ?: '-' }}</td>
                        <td class="wrap">{{ $formatSnWithAsset($lainnya) ?: '-' }}</td>
                    @endif

                    <td>{{ \App\Support\DateFormatter::date($distribution->tanggal_distribusi) }}</td>
                    <td>{{ $distribution->status === 'dipakai' ? 'Dipakai' : 'Dikembalikan' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
