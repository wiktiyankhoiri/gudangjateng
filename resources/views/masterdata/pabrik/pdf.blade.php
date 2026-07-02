<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Pabrik</title>
    <style>
        @page { margin: 30px 25px 50px 25px; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; border-bottom: 3px solid #dc2626; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; color: #dc2626; margin: 0 0 3px 0; text-transform: uppercase; letter-spacing: 2px; }
        .header .sub { font-size: 10px; color: #888; margin: 0; }
        .print-info { text-align: right; font-size: 9px; color: #999; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background: #dc2626; color: #fff; font-size: 9px; padding: 8px 6px; text-transform: uppercase; letter-spacing: 1px; border: 1px solid #b91c1c; }
        td { padding: 6px; border: 1px solid #ddd; font-size: 10px; }
        tr:nth-child(even) { background: #f8f9fa; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

<div class="header">
    <h1>{{ config('app.name') }}</h1>
    <p class="sub">LAPORAN DATA PABRIK</p>
</div>

<div class="print-info">
    Dicetak: {{ date('d/m/Y H:i') }} | Oleh: {{ auth()->user()->nama ?? '-' }}
</div>

<table>
    <thead>
        <tr>
            <th class="text-center" width="6%">NO</th>
                <th class="text-center" width="14%">KODE</th>
            <th width="25%">NAMA PABRIK</th>
            <th width="45%">ALAMAT</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pabrik as $i => $p)
        <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td class="text-center">{{ $p->kode_pabrik }}</td>
            <td>{{ $p->nama_pabrik }}</td>
            <td>{{ $p->alamat ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center" style="padding: 30px; color: #999;">Tidak ada data pabrik</td>
        </tr>
        @endforelse
    </tbody>
</table>

<script type="text/php">
    if (isset($pdf) && isset($fontMetrics)) {
        $pageText = "Halaman $PAGE_NUM dari $PAGE_COUNT";
        $font = $fontMetrics->getFont("Helvetica");
        $size = 9;
        $textWidth = $fontMetrics->getTextWidth($pageText, $font, $size);
        $x = ($pdf->get_width() - $textWidth) / 2;
        $y = $pdf->get_height() - 18;
        $pdf->page_text($x, $y, $pageText, $font, $size, array(0.67, 0.67, 0.67));
    }
</script>

</body>
</html>