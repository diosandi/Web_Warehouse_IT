<!doctype html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th colspan="8">Riwayat Status Barang {{ $item->serial_number }}</th>
            </tr>
            <tr>
                <th>Serial Number</th>
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
            @foreach($statusHistoryEvents as $event)
                <tr>
                    <td>{{ $item->serial_number }}</td>
                    <td>{{ \App\Support\DateFormatter::datetime($event['display_at']) }}</td>
                    <td>{{ $event['event_label'] }}</td>
                    <td>{{ $event['old_status_label'] }}</td>
                    <td>{{ $event['new_status_label'] }}</td>
                    <td>{{ $event['actor_label'] }}: {{ $event['actor'] }}</td>
                    <td>{{ $event['location'] }}</td>
                    <td>{{ $event['note'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
