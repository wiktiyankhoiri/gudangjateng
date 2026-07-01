<?php

namespace App\Http\Controllers;

use App\Models\Stok;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanSalesStokController extends Controller
{
    private function getReportData($tanggalAwal = null, $tanggalAkhir = null)
    {
        // Validate date format strict: only YYYY-MM-DD allowed
        $validDatePattern = '/^\d{4}-\d{2}-\d{2}$/';
        if ($tanggalAwal && ! preg_match($validDatePattern, $tanggalAwal)) {
            $tanggalAwal = null;
        }
        if ($tanggalAkhir && ! preg_match($validDatePattern, $tanggalAkhir)) {
            $tanggalAkhir = null;
        }

        // Build parameterized conditions with ? placeholders
        // Binding order must match the order of ? in SELECT subqueries:
        // c5 (stok_awal_initial), c1 (stok_awal_masuk), c4 (stok_awal_keluar),
        // c6 (stok_awal_penyesuaian), c2 x2 (masuk_pabrik + masuk_retur),
        // c7 x2 (lain_tambah + lain_kurang), c3 (keluar_sales)
        $bindings = [];

        // c5: initialstok.created_at < tanggalAwal
        $c5 = '1=1';
        if ($tanggalAwal) {
            $c5 = 'initialstok.created_at < ?';
            $bindings[] = $tanggalAwal.' 23:59:59';
        }

        // c1: bm.tanggal < tanggalAwal
        $c1 = '1=1';
        if ($tanggalAwal) {
            $c1 = 'bm.tanggal < ?';
            $bindings[] = $tanggalAwal;
        }

        // c4: bk.tanggal < tanggalAwal
        $c4 = '1=1';
        if ($tanggalAwal) {
            $c4 = 'bk.tanggal < ?';
            $bindings[] = $tanggalAwal;
        }

        // c6: ps.tanggal < tanggalAwal
        $c6 = '1=1';
        if ($tanggalAwal) {
            $c6 = 'ps.tanggal < ?';
            $bindings[] = $tanggalAwal;
        }

        // c2: bm.tanggal >= tanggalAwal AND bm.tanggal <= tanggalAkhir
        // Used in TWO subqueries (masuk_pabrik, masuk_retur), so bindings are duplicated
        $c2 = '1=1';
        $c2Bindings = [];
        if ($tanggalAwal && $tanggalAkhir) {
            $c2 = 'bm.tanggal >= ? AND bm.tanggal <= ?';
            $c2Bindings = [$tanggalAwal, $tanggalAkhir];
        } elseif ($tanggalAwal) {
            $c2 = 'bm.tanggal >= ?';
            $c2Bindings = [$tanggalAwal];
        } elseif ($tanggalAkhir) {
            $c2 = 'bm.tanggal <= ?';
            $c2Bindings = [$tanggalAkhir];
        }
        $bindings = array_merge($bindings, $c2Bindings, $c2Bindings); // x2 for masuk_pabrik + masuk_retur

        // c7: ps.tanggal >= tanggalAwal AND ps.tanggal <= tanggalAkhir
        // Used in TWO subqueries (lain_tambah, lain_kurang), so bindings are duplicated
        $c7 = '1=1';
        $c7Bindings = [];
        if ($tanggalAwal && $tanggalAkhir) {
            $c7 = 'ps.tanggal >= ? AND ps.tanggal <= ?';
            $c7Bindings = [$tanggalAwal, $tanggalAkhir];
        } elseif ($tanggalAwal) {
            $c7 = 'ps.tanggal >= ?';
            $c7Bindings = [$tanggalAwal];
        } elseif ($tanggalAkhir) {
            $c7 = 'ps.tanggal <= ?';
            $c7Bindings = [$tanggalAkhir];
        }
        $bindings = array_merge($bindings, $c7Bindings, $c7Bindings); // x2 for lain_tambah + lain_kurang

        // c3: bk.tanggal >= tanggalAwal AND bk.tanggal <= tanggalAkhir
        $c3 = '1=1';
        if ($tanggalAwal && $tanggalAkhir) {
            $c3 = 'bk.tanggal >= ? AND bk.tanggal <= ?';
            $bindings[] = $tanggalAwal;
            $bindings[] = $tanggalAkhir;
        } elseif ($tanggalAwal) {
            $c3 = 'bk.tanggal >= ?';
            $bindings[] = $tanggalAwal;
        } elseif ($tanggalAkhir) {
            $c3 = 'bk.tanggal <= ?';
            $bindings[] = $tanggalAkhir;
        }

        // Single optimized query with subqueries using parameterized bindings
        $query = DB::table('barang as b')
            ->select([
                'b.id',
                'b.kode_barang',
                'b.nama_barang',
                DB::raw("(SELECT COALESCE(SUM(qty_baik + qty_rusak), 0) FROM initialstok WHERE barang_id = b.id AND {$c5}) as stok_awal_initial"),
                DB::raw("(SELECT COALESCE(SUM(bmd.qty_baik + bmd.qty_rusak), 0) FROM barang_masuk_detail bmd JOIN barang_masuk bm ON bm.id = bmd.barang_masuk_id WHERE bmd.barang_id = b.id AND {$c1}) as stok_awal_masuk"),
                DB::raw("(SELECT COALESCE(SUM(bkd.qty_baik), 0) FROM barang_keluar_detail bkd JOIN barang_keluar bk ON bk.id = bkd.barang_keluar_id WHERE bkd.barang_id = b.id AND {$c4}) as stok_awal_keluar"),
                DB::raw("(SELECT COALESCE(SUM(selisih_baik + selisih_rusak), 0) FROM penyesuaian_stok ps WHERE ps.barang_id = b.id AND {$c6}) as stok_awal_penyesuaian"),
                DB::raw("(SELECT COALESCE(SUM(bmd.qty_baik + bmd.qty_rusak), 0) FROM barang_masuk_detail bmd JOIN barang_masuk bm ON bm.id = bmd.barang_masuk_id WHERE bmd.barang_id = b.id AND bm.tipe = 'pabrik' AND {$c2}) as masuk_pabrik"),
                DB::raw("(SELECT COALESCE(SUM(bmd.qty_baik + bmd.qty_rusak), 0) FROM barang_masuk_detail bmd JOIN barang_masuk bm ON bm.id = bmd.barang_masuk_id WHERE bmd.barang_id = b.id AND bm.tipe = 'retur' AND {$c2}) as masuk_retur"),
                DB::raw("(SELECT COALESCE(SUM(selisih_baik + selisih_rusak + selisih_sales), 0) FROM penyesuaian_stok ps WHERE ps.barang_id = b.id AND (ps.selisih_baik + ps.selisih_rusak + ps.selisih_sales) > 0 AND {$c7}) as lain_tambah"),
                DB::raw("(SELECT COALESCE(SUM(ABS(selisih_baik + selisih_rusak + selisih_sales)), 0) FROM penyesuaian_stok ps WHERE ps.barang_id = b.id AND (ps.selisih_baik + ps.selisih_rusak + ps.selisih_sales) < 0 AND {$c7}) as lain_kurang"),
                DB::raw("(SELECT COALESCE(SUM(bkd.qty_baik), 0) FROM barang_keluar_detail bkd JOIN barang_keluar bk ON bk.id = bkd.barang_keluar_id WHERE bkd.barang_id = b.id AND {$c3}) as keluar_sales"),
            ])
            ->setBindings($bindings, 'select');

        $results = $query->orderBy('b.id', 'ASC')->get()->toArray();

        // Fetch current stok in single query (no N+1)
        $allStok = Stok::all();
        $stokMap = [];
        foreach ($allStok as $s) {
            $stokMap[$s->barang_id] = $s;
        }

        // Build result
        $data = [];
        foreach ($results as $row) {
            $stok = $stokMap[$row->id] ?? (object) ['stok_baik' => 0, 'stok_rusak' => 0, 'stok_sales' => 0];
            $stokAwal = (int) $row->stok_awal_initial + (int) $row->stok_awal_masuk - (int) $row->stok_awal_keluar + (int) $row->stok_awal_penyesuaian;
            $totalMasuk = (int) $row->masuk_pabrik + (int) $row->masuk_retur + (int) $row->lain_tambah;
            $totalKeluar = (int) $row->keluar_sales + (int) $row->lain_kurang;

            $data[] = [
                'kode_barang' => $row->kode_barang,
                'nama_barang' => $row->nama_barang,
                'awal' => $stokAwal,
                'pabrik' => (int) $row->masuk_pabrik,
                'retur' => (int) $row->masuk_retur,
                'lain_tambah' => (int) $row->lain_tambah,
                'total_masuk' => $totalMasuk,
                'sales' => (int) $row->keluar_sales,
                'lain_kurang' => (int) $row->lain_kurang,
                'total_keluar' => $totalKeluar,
                'sisa_baik' => (int) $stok->stok_baik,
                'sisa_rusak' => (int) $stok->stok_rusak,
                'sisa_sales' => (int) $stok->stok_sales,
                'total' => (int) $stok->stok_baik + (int) $stok->stok_rusak + (int) $stok->stok_sales,
            ];
        }

        return $data;
    }

    public function index(Request $request)
    {
        $tanggalAwal = $this->normalizeDate($request->get('tanggal_awal'));
        $tanggalAkhir = $this->normalizeDate($request->get('tanggal_akhir'));

        $page = (int) ($request->get('page') ?? 1);
        $perPage = 50;

        if (! $tanggalAwal || ! $tanggalAkhir) {
            $paginator = new LengthAwarePaginator([], 0, $perPage, $page);
            $paginator->withPath('');

            return view('laporan.laporan-sales-stok', [
                'title' => 'Laporan Sales & Stok',
                'data' => $paginator,
                'grandTotals' => [],
                'tanggalAwal' => $tanggalAwal,
                'tanggalAkhir' => $tanggalAkhir,
                'requireDateFilter' => true,
            ]);
        }

        $allData = $this->getReportData($tanggalAwal, $tanggalAkhir);

        // Calculate grand totals from full dataset
        $grandTotals = [
            'awal' => array_sum(array_column($allData, 'awal')),
            'pabrik' => array_sum(array_column($allData, 'pabrik')),
            'retur' => array_sum(array_column($allData, 'retur')),
            'lain_tambah' => array_sum(array_column($allData, 'lain_tambah')),
            'total_masuk' => array_sum(array_column($allData, 'total_masuk')),
            'sales' => array_sum(array_column($allData, 'sales')),
            'lain_kurang' => array_sum(array_column($allData, 'lain_kurang')),
            'total_keluar' => array_sum(array_column($allData, 'total_keluar')),
            'sisa_baik' => array_sum(array_column($allData, 'sisa_baik')),
            'sisa_rusak' => array_sum(array_column($allData, 'sisa_rusak')),
            'sisa_sales' => array_sum(array_column($allData, 'sisa_sales')),
            'total' => array_sum(array_column($allData, 'total')),
        ];

        $total = count($allData);
        $offset = ($page - 1) * $perPage;
        $paginatedData = array_slice($allData, $offset, $perPage);

        $paginator = new LengthAwarePaginator($paginatedData, $total, $perPage, $page);
        $paginator->withPath('');
        $paginator->appends($request->except('page'));

        return view('laporan.laporan-sales-stok', [
            'title' => 'Laporan Sales & Stok',
            'data' => $paginator,
            'grandTotals' => $grandTotals,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
            'requireDateFilter' => false,
        ]);
    }

    public function export(Request $request)
    {
        try {
            $tanggalAwal = $this->normalizeDate($request->get('tanggal_awal'));
            $tanggalAkhir = $this->normalizeDate($request->get('tanggal_akhir'));

            if (! $tanggalAwal || ! $tanggalAkhir) {
                return redirect()->back()->with('error', 'Filter tanggal wajib untuk export.');
            }

            $data = $this->getReportData($tanggalAwal, $tanggalAkhir);

            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();

            // Title
            $sheet->mergeCells('A1:O1');
            $sheet->setCellValue('A1', 'LAPORAN Sales & Stok');

            $sheet->mergeCells('A2:O2');
            $sheet->setCellValue('A2', 'Periode '.date('d F Y', strtotime($tanggalAwal)).' sampai '.date('d F Y', strtotime($tanggalAkhir)));

            $sheet->mergeCells('A3:O3');
            $sheet->setCellValue('A3', 'Cabang JPS Semarang');

            // Header
            $sheet->mergeCells('A4:A5');
            $sheet->mergeCells('B4:B5');
            $sheet->mergeCells('C4:C5');
            $sheet->mergeCells('D4:D5');
            $sheet->mergeCells('E4:H4');
            $sheet->mergeCells('I4:K4');
            $sheet->mergeCells('L4:N4');
            $sheet->mergeCells('O4:O5');

            $sheet->setCellValue('A4', 'NO');
            $sheet->setCellValue('B4', 'KODE');
            $sheet->setCellValue('C4', 'NAMA BARANG');
            $sheet->setCellValue('D4', 'AWAL');
            $sheet->setCellValue('E4', 'BARANG MASUK');
            $sheet->setCellValue('E5', 'PABRIK');
            $sheet->setCellValue('F5', 'RETUR');
            $sheet->setCellValue('G5', 'LAIN2X');
            $sheet->setCellValue('H5', 'TOTAL');
            $sheet->setCellValue('I4', 'BARANG KELUAR');
            $sheet->setCellValue('I5', 'SALES');
            $sheet->setCellValue('J5', 'LAIN2X');
            $sheet->setCellValue('K5', 'TOTAL');
            $sheet->setCellValue('L4', 'SISA STOK');
            $sheet->setCellValue('L5', 'BAIK');
            $sheet->setCellValue('M5', 'RUSAK');
            $sheet->setCellValue('N5', 'SALES');
            $sheet->setCellValue('O4', 'TOTAL');

            // Data
            $row = 6;
            $no = 1;

            foreach ($data as $d) {
                $sheet->setCellValue('A'.$row, $no++);
                $sheet->setCellValue('B'.$row, $d['kode_barang']);
                $sheet->setCellValue('C'.$row, $d['nama_barang']);
                $sheet->setCellValue('D'.$row, $d['awal']);
                $sheet->setCellValue('E'.$row, $d['pabrik']);
                $sheet->setCellValue('F'.$row, $d['retur']);
                $sheet->setCellValue('G'.$row, $d['lain_tambah']);
                $sheet->setCellValue('H'.$row, $d['total_masuk']);
                $sheet->setCellValue('I'.$row, $d['sales']);
                $sheet->setCellValue('J'.$row, $d['lain_kurang']);
                $sheet->setCellValue('K'.$row, $d['total_keluar']);
                $sheet->setCellValue('L'.$row, $d['sisa_baik']);
                $sheet->setCellValue('M'.$row, $d['sisa_rusak']);
                $sheet->setCellValue('N'.$row, $d['sisa_sales']);
                $sheet->setCellValue('O'.$row, $d['total']);
                $row++;
            }

            // Style
            $sheet->getStyle('A1:O5')->getFont()->setBold(true);

            $sheet->getStyle('A4:O5')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->getStyle('A1:A3')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $sheet->getStyle('A6:B'.($row - 1))->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->getStyle('D6:O'.($row - 1))->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->getStyle('A4:O'.($row - 1))->getBorders()
                ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $sheet->getStyle('A4:O5')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('D9EAF7');

            $row += 3;

            // Signature
            $sheet->mergeCells('I'.$row.':L'.$row);
            $sheet->setCellValue('I'.$row, 'SEMARANG, '.date('d F Y'));
            $sheet->getStyle('H'.$row)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $row++;
            $sheet->setCellValue('I'.$row, 'KEPALA GUDANG');
            $sheet->setCellValue('K'.$row, 'KEPALA CABANG');
            $sheet->getStyle('I'.$row.':L'.$row)->getFont()->setBold(true);

            $row += 5;
            $sheet->setCellValue('I'.$row, 'WIKTIYAN KHOIRI');
            $sheet->setCellValue('K'.$row, 'MOH CHOIRUL MUHSON');
            $sheet->getStyle('I'.$row.':L'.$row)->getFont()->setBold(true);

            // Column widths
            foreach (range('A', 'C') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            foreach (range('D', 'O') as $col) {
                $sheet->getColumnDimension($col)->setWidth(10);
            }

            $filename = 'Laporan Sales Stok '.date('YmdHis').'.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $this->getSafeErrorMessage($e));
        }
    }

    private function normalizeDate(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $value = trim($value);

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1) {
            return $value;
        }

        $dt = \DateTime::createFromFormat('d/m/Y', $value);

        if ($dt instanceof \DateTime) {
            return $dt->format('Y-m-d');
        }

        return null;
    }
}
