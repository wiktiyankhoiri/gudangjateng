<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Barang</title>
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
        .text-right { text-align: right; }
        .text-left { text-align: left; }
    </style>
</head>
<body>

<div class="header">
    <h1>{{ config('app.name') }}</h1>
    <p class="sub">LAPORAN DATA BARANG</p>
</div>

<div class="print-info">
    Dicetak: {{ date('d/m/Y H:i') }} | Oleh: {{ auth()->user()->nama ?? '-' }}
</div>

    @php $wHarga = match(count($priceCols)) { 1 => 25, 2 => 18, default => 13 }; @endphp
    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">NO</th>
                <th class="text-center" width="12%">KODE</th>
                <th>NAMA BARANG</th>
                <th class="text-center" width="8%">SATUAN</th>
                <th class="text-center" width="{{ $wHarga }}%">HARGA GOLD</th>
                @if(in_array('harga_grosir', $priceCols))
                <th class="text-center" width="{{ $wHarga }}%">HARGA GROSIR</th>
                @endif
                @if(in_array('harga_khusus', $priceCols))
                <th class="text-center" width="{{ $wHarga }}%">HARGA KHUSUS</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($barang as $i => $b)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td class="text-center">{{ $b->kode_barang }}</td>
                <td>{{ $b->nama_barang }}</td>
                <td class="text-center">{{ strtoupper($b->satuan) }}</td>
                <td class="text-center">{{ $b->harga_gold ? 'Rp ' . number_format($b->harga_gold, 0, ',', '.') : '-' }}</td>
                @if(in_array('harga_grosir', $priceCols))
                <td class="text-center">{{ $b->harga_grosir ? 'Rp ' . number_format($b->harga_grosir, 0, ',', '.') : '-' }}</td>
                @endif
                @if(in_array('harga_khusus', $priceCols))
                <td class="text-center">{{ $b->harga_khusus ? 'Rp ' . number_format($b->harga_khusus, 0, ',', '.') : '-' }}</td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ count($priceCols) + 4 }}" class="text-center" style="padding: 30px; color: #999;">Tidak ada data barang</td>
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