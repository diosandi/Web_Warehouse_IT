<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>History Distribusi {{ $item->serial_number }}</title>
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

        .gray {
            background: #f3f4f6;
            color: #374151;
        }

        .green {
            background: #dcfce7;
            color: #15803d;
        }

        .red {
            background: #fee2e2;
            color: #b91c1c;
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
        <h1>Riwayat Distribusi Barang</h1>
        <p><strong>Serial Number:</strong> {{ $item->serial_number }}</p>
        <p><strong>Barang:</strong> {{ $item->kategori }} / {{ $item->merk ?? '-' }} / {{ $item->type ?? '-' }}</p>
        <p><strong>Dicetak:</strong> {{ now()->format('d-m-Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Pengguna</th>
                <th>Divisi</th>
                <th>Lokasi</th>
                <th>Tanggal Pakai</th>
                <th>Tanggal Pengembalian</th>
                <th>Status</th>
                <th>Kondisi Pengembalian</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($histories as $history)
                @php
                    $returnConditionStatus = $history->return_condition_status;
                    $returnNote = strtolower($history->return_note ?? '');

                    if (
                        $returnConditionStatus !== 'maintenance'
                        && ($returnNote !== '')
                        && (str_contains($returnNote, 'rusak') || str_contains($returnNote, 'maintenance'))
                    ) {
                        $returnConditionStatus = 'maintenance';
                    }
                @endphp
                <tr>
                    <td>{{ $history->distribution->nama_user ?? '-' }}</td>
                    <td>{{ $history->distribution->divisi ?? '-' }}</td>
                    <td>
                        {{ $history->distribution->location->gedung ?? '-' }}
                        -
                        {{ $history->distribution->location->ruangan ?? '-' }}
                    </td>
                    <td>{{ $history->distribution->tanggal_distribusi ?? '-' }}</td>
                    <td>{{ $history->returned_at ? $history->returned_at->format('d-m-Y H:i') : '-' }}</td>
                    <td>
                        @if($history->status === 'dipakai')
                            <span class="badge blue">Dipakai</span>
                        @else
                            <span class="badge gray">Dikembalikan</span>
                        @endif
                    </td>
                    <td>
                        @if($history->status === 'dipakai')
                            <span class="badge gray">-</span>
                        @elseif($returnConditionStatus === 'maintenance')
                            <span class="badge red">Pemeliharaan</span>
                        @else
                            <span class="badge green">Normal</span>
                        @endif
                    </td>
                    <td>
                        {{ $history->distribution->keterangan ?? '-' }}
                        @if($history->return_note)
                            <br><strong>Pengembalian:</strong> {{ $history->return_note }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Tidak ada riwayat distribusi.</td>
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
