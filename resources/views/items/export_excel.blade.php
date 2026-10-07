<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        p { margin: 2px 0; }
        table { border-collapse: collapse; width: 100%; margin-top: 14px; }
        th, td { border: 1px solid #777; padding: 6px; vertical-align: top; }
        th { background: #16a34a; color: #fff; font-weight: bold; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    @include('items.partials.export_table', ['forPdf' => false])
</body>
</html>
