<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Stok;
use App\Models\StokOpname;
use App\Models\StokOpnameDetail;
use App\Services\StokService;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StokOpnameController extends Controller
{
    public function __construct(
        protected StokService $stokService
    ) {}

    public function index(Request $request)
    {
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $status = $request->get('status');

        $query = StokOpname::query()
            ->select('stok_opname.*', 'users.nama as nama_user')
            ->leftJoin('users', 'users.id', '=', 'stok_opname.user_id');

        if ($bulan) {
            $query->whereRaw('EXTRACT(MONTH FROM stok_opname.tanggal_opname) = ?', [(int) $bulan]);
        }
        if ($tahun) {
            $query->whereRaw('EXTRACT(YEAR FROM stok_opname.tanggal_opname) = ?', [(int) $tahun]);
        }
        if ($status) {
            $query->where('stok_opname.status', $status);
        }

        $data = $query->orderBy('stok_opname.id', 'DESC')->paginate(50);

        return view('transaksi.stok-opname.index', [
            'title' => 'Stok Opname',
            'data' => $data,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'status' => $status,
        ]);
    }

    public function create()
    {
        $barang = Barang::orderBy('kode_barang', 'ASC')->get();

        // Ambil semua stok
        $stokRows = DB::table('stok')
            ->join('barang', 'barang.id', '=', 'stok.barang_id')
            ->select('stok.*', 'barang.nama_barang', 'barang.kode_barang')
            ->get();

        $stokIndexed = [];
        foreach ($stokRows as $row) {
            $stokIndexed[$row->barang_id] = (array) $row;
        }

        return view('transaksi.stok-opname.create', [
            'title' => 'Buat Stok Opname',
            'barang' => $barang,
            'stokAll' => $stokIndexed,
        ]);
    }

    public function store(Request $request)
    {
        $tanggalOpname = $request->input('tanggal_opname') ?? now()->format('Y-m-d');
        $catatan = trim($request->input('catatan') ?? '');
        $barangIds = (array) ($request->input('barang_id') ?? []);
        $fisikBaik = (array) ($request->input('stok_fisik_baik') ?? []);
        $fisikRusak = (array) ($request->input('stok_fisik_rusak') ?? []);
        $fisikSales = (array) ($request->input('stok_fisik_sales') ?? []);
        $keteranganDetail = (array) ($request->input('keterangan') ?? []);

        if (empty($barangIds)) {
            return redirect()->back()->withInput()->with('error', 'Detail barang wajib diisi');
        }

        DB::beginTransaction();

        try {
            // Cek duplikat bulan/tahun
            $bulanOpname = date('m', strtotime($tanggalOpname));
            $tahunOpname = date('Y', strtotime($tanggalOpname));
            $sudahAda = StokOpname::whereMonth('tanggal_opname', $bulanOpname)
                ->whereYear('tanggal_opname', $tahunOpname)
                ->where('status', '!=', 'dibatalkan')
                ->exists();

            if ($sudahAda) {
                throw new \App\Exceptions\BusinessException(
                    "Stok opname untuk bulan {$bulanOpname}/{$tahunOpname} sudah ada"
                );
            }

            // Generate nomor opname
            $lastOpname = StokOpname::whereDate('created_at', now()->format('Y-m-d'))
                ->orderBy('id', 'DESC')
                ->first();

            $urutan = $lastOpname ? ((int) substr($lastOpname->no_opname, -3)) + 1 : 1;
            $noOpname = 'SO-' . now()->format('Ymd') . '-' . str_pad($urutan, 3, '0', STR_PAD_LEFT);

            $stokOpname = StokOpname::create([
                'no_opname' => $noOpname,
                'tanggal_opname' => $tanggalOpname,
                'status' => 'draft',
                'catatan' => $catatan,
                'user_id' => auth()->id(),
            ]);

            $totalBarang = 0;

            foreach ($barangIds as $i => $barangId) {
                $barangId = (int) $barangId;
                if ($barangId <= 0) continue;

                $stokSistem = DB::table('stok')->where('barang_id', $barangId)->first();
                $stokSistemBaik = $stokSistem ? (int) $stokSistem->stok_baik : 0;
                $stokSistemRusak = $stokSistem ? (int) $stokSistem->stok_rusak : 0;
                $stokSistemSales = $stokSistem ? (int) $stokSistem->stok_sales : 0;

                $fisikBaikVal = (int) ($fisikBaik[$i] ?? 0);
                $fisikRusakVal = (int) ($fisikRusak[$i] ?? 0);
                $fisikSalesVal = (int) ($fisikSales[$i] ?? 0);

                if ($fisikBaikVal < 0 || $fisikRusakVal < 0 || $fisikSalesVal < 0) {
                    throw new \App\Exceptions\BusinessException('Jumlah tidak boleh negatif');
                }

                if ($fisikBaikVal === 0 && $fisikRusakVal === 0 && $fisikSalesVal === 0) {
                    continue;
                }

                StokOpnameDetail::create([
                    'stok_opname_id' => $stokOpname->id,
                    'barang_id' => $barangId,
                    'stok_sistem_baik' => $stokSistemBaik,
                    'stok_sistem_rusak' => $stokSistemRusak,
                    'stok_sistem_sales' => $stokSistemSales,
                    'stok_fisik_baik' => $fisikBaikVal,
                    'stok_fisik_rusak' => $fisikRusakVal,
                    'stok_fisik_sales' => $fisikSalesVal,
                    'selisih_baik' => $fisikBaikVal - $stokSistemBaik,
                    'selisih_rusak' => $fisikRusakVal - $stokSistemRusak,
                    'selisih_sales' => $fisikSalesVal - $stokSistemSales,
                    'keterangan' => trim($keteranganDetail[$i] ?? ''),
                ]);

                $totalBarang++;
            }

            if ($totalBarang === 0) {
                // Hapus header jika tidak ada detail
                $stokOpname->delete();
                throw new \App\Exceptions\BusinessException('Setidaknya satu barang harus diisi');
            }

            DB::commit();

            $this->writeAuditLog('create', $stokOpname->id, 'Stok opname berhasil dibuat (draft)', [
                'no_opname' => $noOpname,
                'total_barang' => $totalBarang,
            ]);

            return redirect()->route('transaksi.stokopname.detail', $stokOpname->id)
                ->with('success', 'Stok opname berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $this->getSafeErrorMessage($e));
        }
    }

    public function detail($id)
    {
        $opname = StokOpname::query()
            ->select('stok_opname.*', 'users.nama as nama_user')
            ->leftJoin('users', 'users.id', '=', 'stok_opname.user_id')
            ->where('stok_opname.id', $id)
            ->first();

        if (!$opname) {
            return redirect()->route('transaksi.stokopname.index')
                ->with('error', 'Data stok opname tidak ditemukan');
        }

        $detail = StokOpnameDetail::query()
            ->select('stok_opname_detail.*', 'barang.kode_barang', 'barang.nama_barang')
            ->join('barang', 'barang.id', '=', 'stok_opname_detail.barang_id')
            ->where('stok_opname_id', $opname->id)
            ->orderBy('barang.kode_barang', 'ASC')
            ->paginate(50);

        $totalSelisihBaik = $detail->sum('selisih_baik');
        $totalSelisihRusak = $detail->sum('selisih_rusak');
        $totalSelisihSales = $detail->sum('selisih_sales');

        return view('transaksi.stok-opname.detail', [
            'title' => 'Detail Stok Opname',
            'opname' => $opname,
            'detail' => $detail,
            'totalSelisihBaik' => $totalSelisihBaik,
            'totalSelisihRusak' => $totalSelisihRusak,
            'totalSelisihSales' => $totalSelisihSales,
        ]);
    }

    public function selesaikan($id)
    {
        $opname = StokOpname::findOrFail($id);

        if ($opname->status !== 'draft') {
            return redirect()->route('transaksi.stokopname.detail', $id)
                ->with('error', 'Hanya opname status draft yang bisa diselesaikan');
        }

        $opname->update(['status' => 'selesai']);

        $this->writeAuditLog('update', $opname->id, 'Stok opname diselesaikan', [
            'no_opname' => $opname->no_opname,
        ]);

        return redirect()->route('transaksi.stokopname.detail', $id)
            ->with('success', 'Stok opname berhasil diselesaikan');
    }

    public function terapkan($id)
    {
        $opname = StokOpname::with('details')->findOrFail($id);

        if ($opname->status !== 'selesai') {
            return redirect()->route('transaksi.stokopname.detail', $id)
                ->with('error', 'Hanya opname status selesai yang bisa diterapkan');
        }

        DB::beginTransaction();

        try {
            foreach ($opname->details as $d) {
                $stokSistemBaru = (int) $d->stok_fisik_baik;
                $stokRusakBaru = (int) $d->stok_fisik_rusak;
                $stokSalesBaru = (int) ($d->stok_fisik_sales ?? -1);

                $this->stokService->adjustStok($d->barang_id, $stokSistemBaru, $stokRusakBaru, $stokSalesBaru);
            }

            $opname->update(['status' => 'diterapkan']);

            DB::commit();

            $this->writeAuditLog('update', $opname->id, 'Stok opname diterapkan ke stok', [
                'no_opname' => $opname->no_opname,
                'total_detail' => $opname->details->count(),
            ]);

            return redirect()->route('transaksi.stokopname.detail', $id)
                ->with('success', 'Stok opname berhasil diterapkan. Stok telah disesuaikan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('transaksi.stokopname.detail', $id)
                ->with('error', $this->getSafeErrorMessage($e));
        }
    }

    public function batalkan($id)
    {
        $opname = StokOpname::findOrFail($id);

        if (!in_array($opname->status, ['draft', 'selesai'])) {
            return redirect()->route('transaksi.stokopname.detail', $id)
                ->with('error', 'Opname tidak bisa dibatalkan');
        }

        $opname->update(['status' => 'dibatalkan']);

        $this->writeAuditLog('update', $opname->id, 'Stok opname dibatalkan', [
            'no_opname' => $opname->no_opname,
            'status_sebelum' => $opname->getOriginal('status'),
        ]);

        return redirect()->route('transaksi.stokopname.detail', $id)
            ->with('success', 'Stok opname berhasil dibatalkan');
    }

    public function template()
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'kode_barang');
            $sheet->setCellValue('B1', 'nama_barang');
            $sheet->setCellValue('C1', 'stok_sistem_baik');
            $sheet->setCellValue('D1', 'stok_sistem_rusak');
            $sheet->setCellValue('E1', 'stok_fisik_baik');
            $sheet->setCellValue('F1', 'stok_fisik_rusak');
            $sheet->setCellValue('G1', 'keterangan');

            // Isi data barang
            $barang = Barang::orderBy('kode_barang', 'ASC')->get();
            $baris = 2;
            foreach ($barang as $b) {
                $stok = Stok::where('barang_id', $b->id)->first();
                $sheet->setCellValue('A' . $baris, $b->kode_barang);
                $sheet->setCellValue('B' . $baris, $b->nama_barang);
                $sheet->setCellValue('C' . $baris, $stok ? (int) $stok->stok_baik : 0);
                $sheet->setCellValue('D' . $baris, $stok ? (int) $stok->stok_rusak : 0);
                $sheet->setCellValue('E' . $baris, '');
                $sheet->setCellValue('F' . $baris, '');
                $sheet->setCellValue('G' . $baris, '');
                $baris++;
            }

            foreach (range('A', 'G') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'template_stok_opname.xlsx';

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

        $tanggalOpname = $request->input('tanggal_opname') ?? now()->format('Y-m-d');
        $catatan = trim($request->input('catatan') ?? '');

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray();
            unset($rows[0]); // Hapus header

            if (empty($rows)) {
                return redirect()->back()->with('error', 'Data excel kosong');
            }

            DB::beginTransaction();

            // Generate nomor opname
            $lastOpname = StokOpname::whereDate('created_at', now()->format('Y-m-d'))
                ->orderBy('id', 'DESC')
                ->first();

            $urutan = $lastOpname ? ((int) substr($lastOpname->no_opname, -3)) + 1 : 1;
            $noOpname = 'SO-' . now()->format('Ymd') . '-' . str_pad($urutan, 3, '0', STR_PAD_LEFT);

            $stokOpname = StokOpname::create([
                'no_opname' => $noOpname,
                'tanggal_opname' => $tanggalOpname,
                'status' => 'draft',
                'catatan' => $catatan,
                'user_id' => auth()->id(),
            ]);

            $totalImpor = 0;
            $rowNumber = 2;

            foreach ($rows as $row) {
                $kodeBarang = strtoupper(trim($row[0] ?? ''));
                if (empty($kodeBarang)) {
                    $rowNumber++;
                    continue;
                }

                $barang = Barang::where('kode_barang', $kodeBarang)->first();
                if (!$barang) {
                    $rowNumber++;
                    continue;
                }

                $fisikBaik = (int) ($row[4] ?? 0); // Kolom E: stok_fisik_baik
                $fisikRusak = (int) ($row[5] ?? 0); // Kolom F: stok_fisik_rusak
                $ket = trim($row[6] ?? ''); // Kolom G: keterangan

                if ($fisikBaik < 0 || $fisikRusak < 0) {
                    $rowNumber++;
                    continue;
                }

                if ($fisikBaik === 0 && $fisikRusak === 0) {
                    $rowNumber++;
                    continue;
                }

                $stokSistem = Stok::where('barang_id', $barang->id)->first();
                $stokSistemBaik = $stokSistem ? (int) $stokSistem->stok_baik : 0;
                $stokSistemRusak = $stokSistem ? (int) $stokSistem->stok_rusak : 0;

                StokOpnameDetail::create([
                    'stok_opname_id' => $stokOpname->id,
                    'barang_id' => $barang->id,
                    'stok_sistem_baik' => $stokSistemBaik,
                    'stok_sistem_rusak' => $stokSistemRusak,
                    'stok_fisik_baik' => $fisikBaik,
                    'stok_fisik_rusak' => $fisikRusak,
                    'selisih_baik' => $fisikBaik - $stokSistemBaik,
                    'selisih_rusak' => $fisikRusak - $stokSistemRusak,
                    'keterangan' => $ket,
                ]);

                $totalImpor++;
                $rowNumber++;
            }

            if ($totalImpor === 0) {
                $stokOpname->delete();
                throw new \App\Exceptions\BusinessException('Tidak ada data valid yang diimpor');
            }

            DB::commit();

            $this->writeAuditLog('import', $stokOpname->id, 'Stok opname diimpor dari Excel', [
                'no_opname' => $noOpname,
                'total_barang' => $totalImpor,
            ]);

            return redirect()->route('transaksi.stokopname.detail', $stokOpname->id)
                ->with('success', $totalImpor . ' barang berhasil diimpor');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $this->getSafeErrorMessage($e));
        }
    }

    private function normalizeDate(?string $value): ?string
    {
        if (!$value) {
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
