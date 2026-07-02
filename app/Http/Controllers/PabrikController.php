<?php

namespace App\Http\Controllers;

use App\Models\Pabrik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PabrikController extends Controller
{
    public function index(Request $request)
    {
        $this->requireAdmin();
        $keyword = trim($request->get('cari', ''));

        $query = Pabrik::query();

        if ($keyword !== '') {
            $search = str_replace(' ', '', strtoupper($keyword));
            $query->where(function ($q) use ($search) {
                $q->where(DB::raw("REPLACE(kode_pabrik, ' ', '')"), 'like', "%{$search}%")
                  ->orWhere(DB::raw("REPLACE(nama_pabrik, ' ', '')"), 'like', "%{$search}%");
            });
        }

        $pabrik = $query->orderBy('id', 'ASC')->paginate(50);

        if ($request->ajax()) {
            $html = view('masterdata.pabrik._table', compact('pabrik'))->render();
            return response()->json(['html' => $html, 'cari' => $keyword]);
        }

        return view('masterdata.pabrik.index', [
            'title' => 'Data Pabrik',
            'pabrik' => $pabrik,
            'cari' => $keyword,
        ]);
    }

    public function create()
    {
        $this->requireAdmin();
        return view('masterdata.pabrik.create', ['title' => 'Tambah Pabrik']);
    }

    public function store(Request $request)
    {
        $this->requireAdmin();

        $validated = $request->validate([
            'kode_pabrik' => 'required|min:3|unique:pabrik,kode_pabrik',
            'nama_pabrik' => 'required|min:3',
            'alamat' => 'nullable|string',
        ]);

        $validated['kode_pabrik'] = strtoupper(trim($validated['kode_pabrik']));
        $validated['nama_pabrik'] = trim($validated['nama_pabrik']);

        DB::beginTransaction();
        try {
            $pabrik = Pabrik::create($validated);

            DB::commit();

            $this->writeAuditLog('create', $pabrik->id, 'Pabrik berhasil ditambahkan', [
                'kode_pabrik' => $pabrik->kode_pabrik,
                'nama_pabrik' => $pabrik->nama_pabrik,
            ]);

            return redirect()->route('masterdata.pabrik.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $this->getSafeErrorMessage($e));
        }
    }

    public function edit(Pabrik $pabrik)
    {
        $this->requireAdmin();
        return view('masterdata.pabrik.edit', ['title' => 'Edit Pabrik', 'pabrik' => $pabrik]);
    }

    public function update(Request $request, Pabrik $pabrik)
    {
        $this->requireAdmin();

        $validated = $request->validate([
            'kode_pabrik' => 'required|min:3|unique:pabrik,kode_pabrik,' . $pabrik->id,
            'nama_pabrik' => 'required|min:3',
            'alamat' => 'nullable|string',
        ]);

        $validated['kode_pabrik'] = strtoupper(trim($validated['kode_pabrik']));
        $validated['nama_pabrik'] = trim($validated['nama_pabrik']);

        DB::beginTransaction();
        try {
            $pabrik->update($validated);

            DB::commit();

            $this->writeAuditLog('update', $pabrik->id, 'Pabrik berhasil diupdate', [
                'kode_pabrik' => $pabrik->kode_pabrik,
                'nama_pabrik' => $pabrik->nama_pabrik,
            ]);

            return redirect()->route('masterdata.pabrik.index')->with('success', 'Data berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $this->getSafeErrorMessage($e));
        }
    }

    public function destroy(Pabrik $pabrik)
    {
        $this->requireAdmin();

        if ($this->pabrikMemilikiHistori($pabrik->id)) {
            return redirect()->route('masterdata.pabrik.index')->with('error', 'Pabrik tidak boleh dihapus karena sudah memiliki histori transaksi');
        }

        DB::beginTransaction();
        try {
            $pabrik->delete();

            DB::commit();

            $this->writeAuditLog('delete', $pabrik->id, 'Pabrik berhasil dihapus', []);

            return redirect()->route('masterdata.pabrik.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('masterdata.pabrik.index')->with('error', $this->getSafeErrorMessage($e));
        }
    }

    protected function pabrikMemilikiHistori($id): bool
    {
        $count = DB::table('barang_masuk')->where('pabrik_id', $id)->count();
        return $count > 0;
    }

    public function template()
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'kode_pabrik');
            $sheet->setCellValue('B1', 'nama_pabrik');
            $sheet->setCellValue('C1', 'alamat');

            foreach (range('A', 'C') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'template_pabrik.xlsx';

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

                $kodePabrik = strtoupper(trim($row[0] ?? ''));
                $namaPabrik = strtoupper(trim($row[1] ?? ''));
                $alamat = trim($row[2] ?? '');

                if (empty($kodePabrik) || empty($namaPabrik)) {
                    $skipped[] = 'Row ' . $rowNumber . ' : data kosong';
                    $rowNumber++;
                    continue;
                }

                $existing = Pabrik::where('kode_pabrik', $kodePabrik)->first();
                if ($existing) {
                    $existing->update([
                        'nama_pabrik' => $namaPabrik,
                        'alamat' => $alamat,
                    ]);
                    $updated++;
                } else {
                    Pabrik::create([
                        'kode_pabrik' => $kodePabrik,
                        'nama_pabrik' => $namaPabrik,
                        'alamat' => $alamat,
                    ]);
                    $inserted++;
                }

                $rowNumber++;
            }

            DB::commit();

            $this->writeAuditLog('import', null, 'Import data pabrik', [
                'inserted' => $inserted,
                'updated' => $updated,
                'skipped' => $skipped,
            ]);

            $details = [];
            if ($inserted > 0) $details[] = number_format($inserted) . ' data BARU ditambahkan';
            if ($updated > 0) $details[] = number_format($updated) . ' data yang SUDAH ADA diupdate';
            $msg = 'Import selesai. ' . implode(', ', $details) . '.';

            $redirect = redirect()->route('masterdata.pabrik.index')->with('success', $msg);

            if (!empty($skipped)) {
                $redirect->with('warning', array_merge([count($skipped) . ' baris dilewati karena:'], $skipped));
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
            $pabrik = Pabrik::orderBy('id', 'ASC')->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'kode_pabrik');
            $sheet->setCellValue('B1', 'nama_pabrik');
            $sheet->setCellValue('C1', 'alamat');

            $sheet->getStyle('A1:C1')->getFont()->setBold(true);

            $row = 2;
            foreach ($pabrik as $p) {
                $sheet->setCellValue('A' . $row, $p->kode_pabrik);
                $sheet->setCellValue('B' . $row, $p->nama_pabrik);
                $sheet->setCellValue('C' . $row, $p->alamat ?? '');
                $row++;
            }

            foreach (range('A', 'C') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $sheet->freezePane('A2');

            $writer = new Xlsx($spreadsheet);
            $filename = 'pabrik_' . date('Ymd_His') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $this->getSafeErrorMessage($e));
        }
    }
}
