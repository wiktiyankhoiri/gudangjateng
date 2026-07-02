<?php

namespace App\Http\Controllers;

use App\Models\InitialStok;
use App\Models\Barang;
use App\Services\StokService;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class InitialStokController extends Controller
{
    public function __construct(
        protected StokService $stokService
    ) {}

    public function index(Request $request)
    {
        $this->requireAdmin();
        $keyword = trim($request->get('cari', ''));

        $query = InitialStok::query()
            ->join('barang', 'barang.id', '=', 'initialstok.barang_id')
            ->select('initialstok.*', 'barang.kode_barang', 'barang.nama_barang');

        if ($keyword !== '') {
            $search = strtoupper($keyword);
            $query->where(function ($q) use ($search) {
                $q->where('barang.kode_barang', 'like', "%{$search}%")
                  ->orWhere('barang.nama_barang', 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy('initialstok.id', 'ASC')->paginate(50);

        return view('pengaturan.initial-stok.index', [
            'title' => 'Initial Stok',
            'data' => $data,
            'cari' => $keyword,
        ]);
    }

    public function create()
    {
        $this->requireAdmin();
        $barang = Barang::orderBy('nama_barang', 'ASC')->get();

        return view('pengaturan.initial-stok.create', [
            'title' => 'Tambah Initial Stok',
            'barang' => $barang,
        ]);
    }

    public function store(Request $request)
    {
        $this->requireAdmin();

        $qtyBaik = (int) ($request->input('qty_baik') ?? 0);
        $qtyRusak = (int) ($request->input('qty_rusak') ?? 0);

        if ($qtyBaik < 0 || $qtyRusak < 0) {
            return redirect()->back()->withInput()->with('error', 'Jumlah tidak boleh negatif');
        }

        $barangId = (int)($request->input('barang_id') ?? 0);
        if ($barangId <= 0) {
            return redirect()->back()->withInput()->with('error', 'Barang wajib dipilih');
        }

        $keterangan = trim($request->input('keterangan') ?? '');

        DB::beginTransaction();

        try {
            // Check for existing initial stok using FOR UPDATE
            $existing = DB::select(
                'SELECT * FROM initialstok WHERE barang_id = ? FOR UPDATE',
                [$barangId]
            );

            if (!empty($existing)) {
                throw new \Exception('Barang sudah memiliki initial stok');
            }

            $initialStok = InitialStok::create([
                'barang_id' => $barangId,
                'qty_baik' => $qtyBaik,
                'qty_rusak' => $qtyRusak,
                'keterangan' => $keterangan,
            ]);

            $this->stokService->tambahStok($barangId, $qtyBaik, $qtyRusak);

            DB::commit();

            $this->writeAuditLog('create', $initialStok->id, 'Initial stok berhasil disimpan', [
                'barang_id' => $barangId,
                'qty_baik' => $qtyBaik,
                'qty_rusak' => $qtyRusak,
            ]);

            $barang = Barang::find($barangId);
            $barangName = $barang ? $barang->nama_barang : 'Barang';
            $message = $barangName . ' — Baik: ' . $qtyBaik . ', Rusak: ' . $qtyRusak;

            Notification::notify('Initial Stok Baru', $message, 'initialstok', $initialStok->id, ['admin', 'audit']);

            return redirect()->route('pengaturan.initialstok.index')->with('success', 'Berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $this->getSafeErrorMessage($e));
        }
    }

    public function edit($id)
    {
        $this->requireAdmin();

        try {
            $initialStok = InitialStok::findOrFail($id);
        } catch (\Exception $e) {
            return redirect()->route('pengaturan.initialstok.index')->with('error', 'Data initial stok tidak ditemukan');
        }

        $barang = Barang::orderBy('nama_barang', 'ASC')->get();

        return view('pengaturan.initial-stok.edit', [
            'title' => 'Edit Initial Stok',
            'data' => $initialStok,
            'barang' => $barang,
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->requireAdmin();
        $initialStok = InitialStok::findOrFail($id);

        $qtyBaikBaru = (int) ($request->input('qty_baik') ?? 0);
        $qtyRusakBaru = (int) ($request->input('qty_rusak') ?? 0);

        if ($qtyBaikBaru < 0 || $qtyRusakBaru < 0) {
            return redirect()->back()->withInput()->with('error', 'Jumlah tidak boleh negatif');
        }

        $keterangan = trim($request->input('keterangan') ?? '');

        DB::beginTransaction();

        try {
            $stok = $this->stokService->lockStok($initialStok->barang_id);

            if (!$stok) {
                throw new \Exception('Stok barang tidak ditemukan');
            }

            $stokBaikBaru = (int)$stok['stok_baik'] + ($qtyBaikBaru - $initialStok->qty_baik);
            $stokRusakBaru = (int)$stok['stok_rusak'] + ($qtyRusakBaru - $initialStok->qty_rusak);

            if ($stokBaikBaru < 0 || $stokRusakBaru < 0) {
                throw new \Exception('Stok tidak boleh minus');
            }

            $this->stokService->updateStokById((int)$stok['id'], [
                'stok_baik' => $stokBaikBaru,
                'stok_rusak' => $stokRusakBaru,
            ]);

            $oldData = $initialStok->toArray();

            $initialStok->update([
                'qty_baik' => $qtyBaikBaru,
                'qty_rusak' => $qtyRusakBaru,
                'keterangan' => $keterangan,
            ]);

            DB::commit();

            $this->writeAuditLog('update', $initialStok->id, 'Initial stok berhasil diupdate', [
                'barang_id' => $initialStok->barang_id,
                'old' => $oldData,
                'new' => $initialStok->toArray(),
                'stok_sesudah' => ['stok_baik' => $stokBaikBaru, 'stok_rusak' => $stokRusakBaru],
            ]);

            return redirect()->route('pengaturan.initialstok.index')->with('success', 'Berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $this->getSafeErrorMessage($e));
        }
    }

    public function destroy(InitialStok $initialStok)
    {
        $this->requireAdmin();

        DB::beginTransaction();

        try {
            $this->stokService->kurangStok(
                $initialStok->barang_id,
                $initialStok->qty_baik,
                $initialStok->qty_rusak
            );

            $initialStok->delete();

            DB::commit();

            $this->writeAuditLog('delete', $initialStok->id, 'Initial stok berhasil dihapus', [
                'barang_id' => $initialStok->barang_id,
                'deleted' => $initialStok->toArray(),
            ]);

            return redirect()->route('pengaturan.initialstok.index')->with('success', 'Berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pengaturan.initialstok.index')->with('error', $this->getSafeErrorMessage($e));
        }
    }

    public function template()
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'kode_barang');
            $sheet->setCellValue('B1', 'qty_baik');
            $sheet->setCellValue('C1', 'qty_rusak');
            $sheet->setCellValue('D1', 'keterangan');

            $sheet->getStyle('A1:D1')->getFont()->setBold(true);

            foreach (range('A', 'D') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'template_initialstok.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $this->getSafeErrorMessage($e));
        }
    }

    public function export()
    {
        $this->requireAdmin();
        try {
            $items = InitialStok::query()
                ->join('barang', 'barang.id', '=', 'initialstok.barang_id')
                ->select('initialstok.*', 'barang.kode_barang')
                ->orderBy('initialstok.id', 'ASC')
                ->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'kode_barang');
            $sheet->setCellValue('B1', 'qty_baik');
            $sheet->setCellValue('C1', 'qty_rusak');
            $sheet->setCellValue('D1', 'keterangan');

            $sheet->getStyle('A1:D1')->getFont()->setBold(true);

            $row = 2;
            foreach ($items as $item) {
                $sheet->setCellValue('A' . $row, $item->kode_barang);
                $sheet->setCellValue('B' . $row, $item->qty_baik);
                $sheet->setCellValue('C' . $row, $item->qty_rusak);
                $sheet->setCellValue('D' . $row, $item->keterangan ?? '');
                $row++;
            }

            foreach (range('A', 'D') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $sheet->freezePane('A2');

            $writer = new Xlsx($spreadsheet);
            $filename = 'initialstok_' . date('Ymd_His') . '.xlsx';

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
        $this->requireAdmin();
        $file = $request->file('file_excel');

        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid');
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
            $skipped = [];
            $rowNumber = 2;

            DB::beginTransaction();

            foreach ($rows as $row) {
                if (
                    trim($row[0] ?? '') === '' &&
                    trim($row[1] ?? '') === '' &&
                    trim($row[2] ?? '') === '' &&
                    trim($row[3] ?? '') === ''
                ) {
                    $rowNumber++;
                    continue;
                }

                $kodeBarang = strtoupper(trim($row[0] ?? ''));
                $qtyBaikRaw = trim($row[1] ?? '');
                $qtyRusakRaw = trim($row[2] ?? '');
                $keterangan = trim($row[3] ?? '');

                $qtyBaik = $qtyBaikRaw === '' ? 0 : (is_numeric($qtyBaikRaw) ? (int) $qtyBaikRaw : null);
                $qtyRusak = $qtyRusakRaw === '' ? 0 : (is_numeric($qtyRusakRaw) ? (int) $qtyRusakRaw : null);

                if (empty($kodeBarang)) {
                    $skipped[] = 'Row ' . $rowNumber . ' : kode_barang wajib diisi';
                    $rowNumber++;
                    continue;
                }

                if ($qtyBaik === null || $qtyRusak === null) {
                    $skipped[] = 'Row ' . $rowNumber . ' : qty_baik atau qty_rusak tidak valid';
                    $rowNumber++;
                    continue;
                }

                if ($qtyBaik < 0 || $qtyRusak < 0) {
                    $skipped[] = 'Row ' . $rowNumber . ' : qty tidak boleh negatif';
                    $rowNumber++;
                    continue;
                }

                $barang = Barang::where('kode_barang', $kodeBarang)->first();
                if (!$barang) {
                    $skipped[] = 'Row ' . $rowNumber . ' : Barang tidak ditemukan';
                    $rowNumber++;
                    continue;
                }

                $existing = DB::select(
                    'SELECT * FROM initialstok WHERE barang_id = ? FOR UPDATE',
                    [(int)$barang->id]
                );

                if (!empty($existing)) {
                    $skipped[] = 'Row ' . $rowNumber . ' : Barang sudah memiliki initial stok';
                    $rowNumber++;
                    continue;
                }

                $initialStok = InitialStok::create([
                    'barang_id' => $barang->id,
                    'qty_baik' => $qtyBaik,
                    'qty_rusak' => $qtyRusak,
                    'keterangan' => $keterangan,
                ]);

                $this->stokService->tambahStok((int)$barang->id, $qtyBaik, $qtyRusak);

                $inserted++;
                $rowNumber++;
            }

            DB::commit();

            $this->writeAuditLog('import', null, 'Import initial stok berhasil', [
                'inserted' => $inserted,
                'skipped' => $skipped,
            ]);

            $redirect = redirect()->route('pengaturan.initialstok.index')
                ->with('success', 'Import selesai. ' . $inserted . ' data baru ditambahkan.');

            if (!empty($skipped)) {
                $redirect->with('warning', array_merge([count($skipped) . ' baris dilewati karena:'], $skipped));
            }

            return $redirect;
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $this->getSafeErrorMessage($e));
        }
    }
}
