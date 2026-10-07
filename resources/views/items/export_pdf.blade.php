<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Data Barang</title>
    <style>
        @page { size: landscape; margin: 12mm; }
        body { font-family: Arial, sans-serif; color: #111827; margin: 24px; font-size: 11px; }
        h1 { margin: 0 0 4px; font-size: 24px; }
        p { margin: 4px 0; }
        table { border-collapse: collapse; width: 100%; margin-top: 14px; }
        th, td { border: 1px solid #d1d5db; padding: 6px; text-align: left; vertical-align: top; }
        th { background: #166534; color: #fff; }
        .summary { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin: 18px 0; }
        .card { border: 1px solid #d1d5db; border-left: 4px solid #16a34a; padding: 10px; }
        .label { color: #6b7280; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .value { font-size: 18px; font-weight: bold; margin-top: 4px; }
        .no-print { margin-bottom: 20px; }
        button { background: #16a34a; color: #fff; border: 0; border-radius: 6px; padding: 10px 14px; font-weight: bold; cursor: pointer; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>

    @include('items.partials.export_table', ['forPdf' => true])
</body>
</html>
