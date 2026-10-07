@php
    $statusLabels = [
        'available' => 'Tersedia',
        'used' => 'Digunakan',
        'maintenance' => 'Pemeliharaan',
        'retired' => 'Tidak Digunakan',
        'vendor' => 'Dibawa Vendor',
    ];
@endphp

<h1>Master Data Barang</h1>
<p>Filter: <strong>{{ $filterLabel }}</strong></p>
<p>Total item: <strong>{{ number_format($items->count()) }}</strong></p>
<p>Tanggal export: {{ now()->format('d-m-Y H:i') }}</p>

@if($forPdf ?? false)
    <div class="summary">
        <div class="card">
            <div class="label">Total Item</div>
            <div class="value">{{ number_format($items->count()) }}</div>
        </div>
        <div class="card">
            <div class="label">Filter</div>
            <div class="value">{{ $filterLabel }}</div>
        </div>
    </div>
@endif

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kategori</th>
            <th>Asal Data</th>
            <th>Asset</th>
            <th>Merk</th>
            <th>Tipe</th>
            <th>Serial Number</th>
            <th>Service Tag</th>
            <th>OS</th>
            <th>Processor</th>
            <th>RAM</th>
            <th>Storage</th>
            <th>VGA</th>
            <th>Tahun</th>
            <th>Lokasi Saat Ini</th>
            <th>Kondisi</th>
            <th>Keterangan Kondisi</th>
            <th>Tanggal Input</th>
            <th>Terakhir Diubah</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $index => $item)
            @php
                $activeDistributionItem = $item->distributionItems->first();
                $currentLocation = $activeDistributionItem?->distribution?->location ?? $item->storageLocation;
                $currentLocationLabel = $currentLocation
                    ? collect([$currentLocation->gedung, $currentLocation->ruangan])->filter()->implode(' - ')
                    : '-';
                $currentLocationLabel = $currentLocationLabel !== '' ? $currentLocationLabel : '-';
                $displayStatus = $activeDistributionItem
                    ? 'used'
                    : ($item->status === 'used' ? 'available' : $item->status);
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->kategori ?? '-' }}</td>
                <td>{{ $item->barang_masuk_id ? 'Barang Masuk' : 'Master Item' }}</td>
                <td>{{ $item->asset ?? '-' }}</td>
                <td>{{ $item->merk ?? '-' }}</td>
                <td>{{ $item->type ?? '-' }}</td>
                <td>{{ $item->serial_number ?? '-' }}</td>
                <td>{{ $item->service_tag ?? '-' }}</td>
                <td>{{ $item->os ?? '-' }}</td>
                <td>{{ $item->processor ?? '-' }}</td>
                <td>{{ $item->ram_gb ? $item->ram_gb . ' GB' : '-' }}</td>
                <td>{{ $item->storage_gb ? $item->storage_gb . ' GB' : '-' }}</td>
                <td>{{ $item->vga ?? '-' }}</td>
                <td>{{ $item->tahun ?? '-' }}</td>
                <td>{{ $currentLocationLabel }}</td>
                <td>{{ $statusLabels[$displayStatus] ?? ($displayStatus ?? '-') }}</td>
                <td>{{ $item->condition_note ?? '-' }}</td>
                <td>{{ \App\Support\DateFormatter::date($item->barang_masuk?->tanggal_masuk ?? $item->created_at) }}</td>
                <td>{{ \App\Support\DateFormatter::datetime($item->updated_at) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="19">Tidak ada data master barang.</td>
            </tr>
        @endforelse
    </tbody>
</table>
