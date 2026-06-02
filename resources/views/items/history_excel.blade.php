<!doctype html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th colspan="8">Riwayat Distribusi {{ $item->serial_number }}</th>
            </tr>
            <tr>
                <th>Serial Number</th>
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
            @foreach($histories as $history)
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
                    <td>{{ $item->serial_number }}</td>
                    <td>{{ $history->distribution->nama_user ?? '-' }}</td>
                    <td>{{ $history->distribution->divisi ?? '-' }}</td>
                    <td>
                        {{ $history->distribution->location->gedung ?? '-' }}
                        -
                        {{ $history->distribution->location->ruangan ?? '-' }}
                    </td>
                    <td>{{ $history->distribution->tanggal_distribusi ?? '-' }}</td>
                    <td>{{ $history->returned_at ? $history->returned_at->format('d-m-Y H:i') : '-' }}</td>
                    <td>{{ $history->status === 'dipakai' ? 'Dipakai' : 'Dikembalikan' }}</td>
                    <td>
                        @if($history->status === 'dipakai')
                            -
                        @elseif($returnConditionStatus === 'maintenance')
                            Pemeliharaan
                        @else
                            Normal
                        @endif
                    </td>
                    <td>
                        {{ $history->distribution->keterangan ?? '-' }}
                        @if($history->return_note)
                            / Pengembalian: {{ $history->return_note }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
