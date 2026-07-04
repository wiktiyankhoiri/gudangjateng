<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Stok;
use App\Services\StokService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangController extends Controller
{
    public function __construct(
        protected StokService $stokService
    ) {}

    public function index(Request $request)
    {
        $keyword = trim($request->get('cari', ''));

        $query = Barang::query();

        if ($keyword !== '') {
            $search = str_replace(' ', '', strtoupper($keyword));
            $query->where(function ($q) use ($search) {
                $q->where(DB::raw("REPLACE(kode_barang, ' ', '')"), 'like', "%{$search}%")
                  ->orWhere(DB::raw("REPLACE(nama_barang, ' ', '')"), 'like', "%{$search}%");
            });
        }

        $barang = $query->orderBy('id', 'ASC')->paginate(50);

        $userRole = auth()->user()->role;

        if ($request->ajax()) {
            $html = view('masterdata.barang._table', compact('barang', 'userRole'))->render();
            return response()->json([
                'html' => $html,
                'cari' => $keyword,
            ]);
        }

        return view('masterdata.barang.index', [
            'title' => 'Data Barang',
            'barang' => $barang,
            'cari' => $keyword,
            'userRole' => $userRole,
        ]);
    }

    public function create()
    {
        $this->requireAdmin();
        return view('masterdata.barang.create', ['title' => 'Tambah Barang']);
    }

    public function store(Request $request)
    {
        $this->requireAdmin();

        $validated = $request->validate([
            'kode_barang' => 'required|min:3|unique:barang,kode_barang',
            'nama_barang' => 'required|min:3',
            'satuan' => 'required|uppercase|in:PCS,SET',
            'harga_gold' => 'nullable|numeric|min:0|max:9999999999999',
            'harga_grosir' => 'nullable|numeric|min:0|max:9999999999999',
            'harga_khusus' => 'nullable|numeric|min:0|max:9999999999999',
        ]);

        $validated['kode_barang'] = strtoupper(trim($validated['kode_barang']));
        $validated['nama_barang'] = strtoupper(trim($validated['nama_barang']));

        DB::beginTransaction();
        try {
            $barang = Barang::create($validated);

            Stok::create([
                'barang_id' => $barang->id,
                'stok_baik' => 0,
                'stok_rusak' => 0,
                'updated_at' => now(),
            ]);

            DB::commit();

            $this->writeAuditLog('create', $barang->id, 'Barang berhasil ditambahkan', [
                'kode_barang' => $barang->kode_barang,
                'nama_barang' => $barang->nama_barang,
            ]);

            return redirect()->route('masterdata.barang.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $this->getSafeErrorMessage($e));
        }
    }

    public function edit(Barang $barang)
    {
        $this->requireAdmin();
        return view('masterdata.barang.edit', ['title' => 'Edit Barang', 'barang' => $barang]);
    }

    public function update(Request $request, Barang $barang)
    {
        $this->requireAdmin();

        $validated = $request->validate([
            'kode_barang' => 'required|min:3|unique:barang,kode_barang,' . $barang->id,
            'nama_barang' => 'required|min:3',
            'satuan' => 'required|uppercase|in:PCS,SET',
            'harga_gold' => 'nullable|numeric|min:0|max:9999999999999',
            'harga_grosir' => 'nullable|numeric|min:0|max:9999999999999',
            'harga_khusus' => 'nullable|numeric|min:0|max:9999999999999',
        ]);

        $validated['kode_barang'] = strtoupper(trim($validated['kode_barang']));
        $validated['nama_barang'] = strtoupper(trim($validated['nama_barang']));

        DB::beginTransaction();
        try {
            $barang->update($validated);

            DB::commit();

            $this->writeAuditLog('update', $barang->id, 'Barang berhasil diupdate', [
                'kode_barang' => $barang->kode_barang,
                'nama_barang' => $barang->nama_barang,
            ]);

            return redirect()->route('masterdata.barang.index')->with('success', 'Data berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $this->getSafeErrorMessage($e));
        }
    }

    public function destroy(Barang $barang)
    {
        $this->requireAdmin();

        if ($this->barangMemilikiHistori($barang->id)) {
            return redirect()->route('masterdata.barang.index')->with('error', 'Barang tidak boleh dihapus karena sudah memiliki histori transaksi');
        }

        DB::beginTransaction();
        try {
            Stok::where('barang_id', $barang->id)->delete();
            $barang->delete();

            DB::commit();

            $this->writeAuditLog('delete', $barang->id, 'Barang berhasil dihapus', []);

            return redirect()->route('masterdata.barang.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('masterdata.barang.index')->with('error', $this->getSafeErrorMessage($e));
        }
    }

    protected function barangMemilikiHistori($id): bool
    {
        $tables = [
            'barang_masuk_detail',
            'barang_keluar_detail',
            'mutasi_detail',
            'penyesuaian_stok',
            'initialstok',
        ];

        foreach ($tables as $table) {
            $count = DB::table($table)->where('barang_id', $id)->count();
            if ($count > 0) {
                return true;
            }
        }

        return false;
    }

    protected function getPriceColumnsForRole(): array
    {
        $role = auth()->user()->role;

        if (in_array($role, ['super_admin', 'admin'])) {
            return ['harga_gold', 'harga_grosir', 'harga_khusus'];
        }

        if (in_array($role, ['manager', 'audit'])) {
            return ['harga_gold', 'harga_grosir'];
        }

        return ['harga_gold'];
    }

    public function template()
    {
        try {
            $priceCols = $this->getPriceColumnsForRole();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'kode_barang');
            $sheet->setCellValue('B1', 'nama_barang');
            $sheet->setCellValue('C1', 'satuan');

            $colIndex = 'D';
            $headers = ['harga_gold' => 'harga_gold', 'harga_grosir' => 'harga_grosir', 'harga_khusus' => 'harga_khusus'];
            foreach ($priceCols as $col) {
                $sheet->setCellValue($colIndex . '1', $headers[$col]);
                $colIndex++;
            }

            $lastCol = $colIndex === 'D' ? 'C' : chr(ord('C') + count($priceCols));
            foreach (range('A', $lastCol) as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'template_barang.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $this->getSafeErrorMessage($e));
        }
    }

    public function import(Request $request)
    {
        $file = $request->file('file_excel');

        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid');
        }

        // Batas ukuran file: 5MB
        if ($file->getSize() > 5 * 1024 * 1024) {
            return redirect()->back()->with('error', 'File maksimal 5MB');
        }

        // Validasi MIME type
        $allowedMimes = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel',
        ];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return redirect()->back()->with('error', 'Format file harus Excel (.xls/.xlsx)');
        }

        $extension = strtolower($file->extension());
        if (!in_array($extension, ['xls', 'xlsx'])) {
            return redirect()->back()->with('error', 'Format file harus xls atau xlsx');
        }

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray();
            unset($rows[0]);

            if (empty($rows)) {
                return redirect()->back()->with('error', 'Data excel kosong');
            }

            $inserted = 0;
            $updated = 0;
            $skipped = [];
            $rowNumber = 2;

            DB::beginTransaction();

            foreach ($rows as $row) {
                if (trim($row[0] ?? '') === '' && trim($row[1] ?? '') === '' && trim($row[2] ?? '') === '') {
                    $rowNumber++;
                    continue;
                }

                $kodeBarang = strtoupper(trim($row[0] ?? ''));
                $namaBarang = strtoupper(trim($row[1] ?? ''));
                $satuan = strtoupper(trim($row[2] ?? ''));

                if (empty($kodeBarang) || empty($namaBarang)) {
                    $skipped[] = "Row {$rowNumber} : kode dan nama barang kosong, dilewati";
                    $rowNumber++;
                    continue;
                }

                if (empty($satuan)) {
                    $satuan = 'PCS';
                }

                if (!in_array($satuan, ['PCS', 'SET'])) {
                    $skipped[] = "Row {$rowNumber} : satuan '{$satuan}' tidak valid (hanya PCS/SET)";
                    $rowNumber++;
                    continue;
                }

                $parseHarga = function ($val) {
                    if ($val === null || $val === '') return null;
                    if (is_numeric($val)) {
                        $num = (int) $val;
                        return $num === 0 ? null : $num;
                    }
                    $num = (int) str_replace('.', '', str_replace(',', '', $val));
                    return $num === 0 ? null : $num;
                };
                $hargaGold   = $parseHarga($row[3] ?? null);
                $hargaGrosir = $parseHarga($row[4] ?? null);
                $hargaKhusus = $parseHarga($row[5] ?? null);

                $data = [
                    'nama_barang' => $namaBarang,
                    'satuan' => $satuan,
                ];
                if ($hargaGold !== null) $data['harga_gold'] = $hargaGold;
                if ($hargaGrosir !== null) $data['harga_grosir'] = $hargaGrosir;
                if ($hargaKhusus !== null) $data['harga_khusus'] = $hargaKhusus;

                $existing = Barang::where('kode_barang', $kodeBarang)->first();
                if ($existing) {
                    $existing->update($data);
                    $updated++;
                } else {
                    $skipped[] = "Row {$rowNumber} : kode [{$kodeBarang}] - {$namaBarang} tidak ditemukan. Buat barang dulu lewat form Tambah.";
                }

                $rowNumber++;
            }

            DB::commit();

            $details = [];
            if ($inserted > 0) $details[] = number_format($inserted) . ' data BARU ditambahkan';
            if ($updated > 0) $details[] = number_format($updated) . ' data yang SUDAH ADA diupdate';
            $msg = 'Import selesai. ' . implode(', ', $details) . '.';

            $this->writeAuditLog('import', null, 'Import data barang', [
                'inserted' => $inserted,
                'updated' => $updated,
                'skipped' => $skipped,
            ]);

            $redirect = redirect()->route('masterdata.barang.index')->with('success', $msg);

            if (!empty($skipped)) {
                $summary = count($skipped) . ' baris dilewati karena:';
                $redirect->with('warning', array_merge([$summary], $skipped));
            }

            return $redirect;
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $this->getSafeErrorMessage($e));
        }
    }

    public function export()
    {
        try {
            $priceCols = $this->getPriceColumnsForRole();
            $labelMap = ['harga_gold' => 'harga_gold', 'harga_grosir' => 'harga_grosir', 'harga_khusus' => 'harga_khusus'];

            $barang = Barang::orderBy('id', 'ASC')->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'kode_barang');
            $sheet->setCellValue('B1', 'nama_barang');
            $sheet->setCellValue('C1', 'satuan');

            $colIndex = 'D';
            foreach ($priceCols as $col) {
                $sheet->setCellValue($colIndex . '1', $labelMap[$col]);
                $colIndex++;
            }

            $headerRange = 'A1:' . ($colIndex === 'D' ? 'C' : chr(ord('C') + count($priceCols))) . '1';
            $sheet->getStyle($headerRange)->getFont()->setBold(true);

            $row = 2;
            foreach ($barang as $b) {
                $sheet->setCellValue('A' . $row, $b->kode_barang);
                $sheet->setCellValue('B' . $row, $b->nama_barang);
                $sheet->setCellValue('C' . $row, strtoupper($b->satuan));

                $ci = 'D';
                foreach ($priceCols as $col) {
                    $sheet->setCellValue($ci . $row, $b->{$col} ?? '');
                    $ci++;
                }
                $row++;
            }

            $lastColForSize = $colIndex === 'D' ? 'C' : chr(ord('C') + count($priceCols));
            foreach (range('A', $lastColForSize) as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $sheet->freezePane('A2');

            $writer = new Xlsx($spreadsheet);
            $filename = 'barang_' . date('Ymd_His') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $this->getSafeErrorMessage($e));
        }
    }

    public function exportPdf()
    {
        try {
            $priceCols = $this->getPriceColumnsForRole();
            $labelMap = ['harga_gold' => 'Harga Gold', 'harga_grosir' => 'Harga Grosir', 'harga_khusus' => 'Harga Khusus'];

            $barang = Barang::orderBy('id', 'ASC')->get();
            $userRole = auth()->user()->role;

            $pdf = Pdf::loadView('masterdata.barang.pdf', compact('barang', 'priceCols', 'labelMap', 'userRole'));
            $pdf->setPaper('A4', 'landscape');

            return $pdf->download('barang_' . date('Ymd_His') . '.pdf');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $this->getSafeErrorMessage($e));
        }
    }
}
