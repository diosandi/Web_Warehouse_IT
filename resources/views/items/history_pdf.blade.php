<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Status Barang {{ $item->serial_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #111827;
            margin: 24px;
        }

        .header {
            border-bottom: 2px solid #15803d;
            margin-bottom: 16px;
            padding-bottom: 12px;
        }

        h1 {
            font-size: 22px;
            margin: 0 0 6px;
        }

        p {
            margin: 3px 0;
            font-size: 12px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 11px;
        }

        th {
            background: #15803d;
            color: #ffffff;
            text-align: left;
            padding: 8px;
            border: 1px solid #d1d5db;
        }

        td {
            padding: 7px;
            border: 1px solid #d1d5db;
            vertical-align: top;
        }

        .badge {
            border-radius: 999px;
            display: inline-block;
            font-weight: 700;
            padding: 3px 8px;
        }

        .blue {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .green {
            background: #dcfce7;
            color: #15803d;
        }

        .purple {
            background: #f3e8ff;
            color: #7e22ce;
        }

        .gray {
            background: #f3f4f6;
            color: #374151;
        }

        .actions {
            margin-bottom: 16px;
        }

        .actions button {
            background: #15803d;
            border: 0;
            border-radius: 6px;
            color: #ffffff;
            cursor: pointer;
            font-weight: 700;
            padding: 8px 12px;
        }

        @media print {
            body {
                margin: 12mm;
            }

            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>

    <div class="header">
        <h1>Riwayat Status Barang</h1>
        <p><strong>Serial Number:</strong> {{ $item->serial_number }}</p>
        <p><strong>Barang:</strong> {{ $item->kategori }} / {{ $item->merk ?? '-' }} / {{ $item->type ?? '-' }}</p>
        <p><strong>Dicetak:</strong> {{ now()->format('d-m-Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Aktivitas</th>
                <th>Status Lama</th>
                <th>Status Baru</th>
                <th>Pengguna/Oleh</th>
                <th>Lokasi</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($statusHistoryEvents as $event)
                @php
                    $eventClass = [
                        'distribution' => 'blue',
                        'return' => 'green',
                        'manual' => 'purple',
                    ][$event['event_type']] ?? 'gray';
                @endphp
                <tr>
                    <td>{{ \App\Support\DateFormatter::datetime($event['display_at']) }}</td>
                    <td><span class="badge {{ $eventClass }}">{{ $event['event_label'] }}</span></td>
                    <td>{{ $event['old_status_label'] }}</td>
                    <td>{{ $event['new_status_label'] }}</td>
                    <td>{{ $event['actor_label'] }}: {{ $event['actor'] }}</td>
                    <td>{{ $event['location'] }}</td>
                    <td>{{ $event['note'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Tidak ada riwayat status barang.</td>
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
