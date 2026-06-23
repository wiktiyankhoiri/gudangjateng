<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TokoController extends Controller
{
    public function index(Request $request)
    {
        $this->requireAdmin();
        $keyword = trim($request->get('q', ''));

        $query = Toko::query();

        if ($keyword !== '') {
            $search = str_replace(' ', '', strtoupper($keyword));
            $query->where(function ($q) use ($search) {
                $q->where(DB::raw("REPLACE(kode_toko, ' ', '')"), 'like', "%{$search}%")
                  ->orWhere(DB::raw("REPLACE(nama_toko, ' ', '')"), 'like', "%{$search}%");
            });
        }

        $toko = $query->orderBy('id', 'ASC')->paginate(50);

        if ($request->ajax()) {
            $html = view('masterdata.toko._table', compact('toko'))->render();
            return response()->json(['html' => $html, 'q' => $keyword]);
        }

        return view('masterdata.toko.index', [
            'title' => 'Data Toko',
            'toko' => $toko,
            'q' => $keyword,
        ]);
    }

    public function create()
    {
        $this->requireAdmin();
        return view('masterdata.toko.create', ['title' => 'Tambah Toko']);
    }

    public function store(Request $request)
    {
        $this->requireAdmin();

        $request->merge([
            'kode_toko' => strtoupper(trim($request->kode_toko)),
            'nama_toko' => trim($request->nama_toko),
        ]);

        $validated = $request->validate([
            'kode_toko' => 'required|min:3|unique:toko,kode_toko',
            'nama_toko' => 'required|min:3',
            'alamat' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $toko = Toko::create($validated);

            DB::commit();

            $this->writeAuditLog('create', $toko->id, 'Toko berhasil ditambahkan', [
                'kode_toko' => $toko->kode_toko,
                'nama_toko' => $toko->nama_toko,
            ]);

            return redirect()->route('masterdata.toko.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $this->getSafeErrorMessage($e));
        }
    }

    public function edit(Toko $toko)
    {
        $this->requireAdmin();
        return view('masterdata.toko.edit', ['title' => 'Edit Toko', 'toko' => $toko]);
    }

    public function update(Request $request, Toko $toko)
    {
        $this->requireAdmin();

        $request->merge([
            'kode_toko' => strtoupper(trim($request->kode_toko)),
            'nama_toko' => trim($request->nama_toko),
        ]);

        $validated = $request->validate([
            'kode_toko' => 'required|min:3|unique:toko,kode_toko,' . $toko->id,
            'nama_toko' => 'required|min:3',
            'alamat' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $toko->update($validated);

            DB::commit();

            $this->writeAuditLog('update', $toko->id, 'Toko berhasil diupdate', [
                'kode_toko' => $toko->kode_toko,
                'nama_toko' => $toko->nama_toko,
            ]);

            return redirect()->route('masterdata.toko.index')->with('success', 'Data berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $this->getSafeErrorMessage($e));
        }
    }

    public function destroy(Toko $toko)
    {
        $this->requireAdmin();

        if ($this->tokoMemilikiHistori($toko->id)) {
            return redirect()->route('masterdata.toko.index')->with('error', 'Toko tidak boleh dihapus karena sudah memiliki histori transaksi');
        }

        DB::beginTransaction();
        try {
            $toko->delete();

            DB::commit();

            $this->writeAuditLog('delete', $toko->id, 'Toko berhasil dihapus', []);

            return redirect()->route('masterdata.toko.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('masterdata.toko.index')->with('error', $this->getSafeErrorMessage($e));
        }
    }

    protected function tokoMemilikiHistori($id): bool
    {
        $tables = [
            'barang_masuk',
            'barang_keluar',
        ];

        foreach ($tables as $table) {
            $count = DB::table($table)->where('toko_id', $id)->count();
            if ($count > 0) {
                return true;
            }
        }

        return false;
    }

    public function template()
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'kode_toko');
            $sheet->setCellValue('B1', 'nama_toko');
            $sheet->setCellValue('C1', 'alamat');

            foreach (range('A', 'C') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'template_toko.xlsx';

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

                $kodeToko = strtoupper(trim($row[0] ?? ''));
                $namaToko = strtoupper(trim($row[1] ?? ''));
                $alamat = trim($row[2] ?? '');

                if (empty($kodeToko) || empty($namaToko)) {
                    $skipped[] = 'Row ' . $rowNumber . ' : data kosong';
                    $rowNumber++;
                    continue;
                }

                $existing = Toko::where('kode_toko', $kodeToko)->first();
                if ($existing) {
                    $existing->update([
                        'nama_toko' => $namaToko,
                        'alamat' => $alamat,
                    ]);
                    $updated++;
                } else {
                    Toko::create([
                        'kode_toko' => $kodeToko,
                        'nama_toko' => $namaToko,
                        'alamat' => $alamat,
                    ]);
                    $inserted++;
                }

                $rowNumber++;
            }

            DB::commit();

            $this->writeAuditLog('import', null, 'Import data toko', [
                'inserted' => $inserted,
                'updated' => $updated,
                'skipped' => $skipped,
            ]);

            $details = [];
            if ($inserted > 0) $details[] = number_format($inserted) . ' data BARU ditambahkan';
            if ($updated > 0) $details[] = number_format($updated) . ' data yang SUDAH ADA diupdate';
            $msg = 'Import selesai. ' . implode(', ', $details) . '.';

            $redirect = redirect()->route('masterdata.toko.index')->with('success', $msg);

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
            $toko = Toko::orderBy('id', 'ASC')->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'kode_toko');
            $sheet->setCellValue('B1', 'nama_toko');
            $sheet->setCellValue('C1', 'alamat');

            $sheet->getStyle('A1:C1')->getFont()->setBold(true);

            $row = 2;
            foreach ($toko as $t) {
                $sheet->setCellValue('A' . $row, $t->kode_toko);
                $sheet->setCellValue('B' . $row, $t->nama_toko);
                $sheet->setCellValue('C' . $row, $t->alamat ?? '');
                $row++;
            }

            foreach (range('A', 'C') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $sheet->freezePane('A2');

            $writer = new Xlsx($spreadsheet);
            $filename = 'toko_' . date('Ymd_His') . '.xlsx';

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
