<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PenyesuaianStok;
use App\Services\StokService;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenyesuaianStokController extends Controller
{
    public function __construct(
        protected StokService $stokService
    ) {}

    public function index(Request $request)
    {
        $this->requireAudit();

        $tanggalAwal = $this->normalizeDate($request->get('tanggal_awal'));
        $tanggalAkhir = $this->normalizeDate($request->get('tanggal_akhir'));

        $query = PenyesuaianStok::query()
            ->select('penyesuaian_stok.*', 'barang.kode_barang', 'barang.nama_barang', 'users.nama as nama_user')
            ->join('barang', 'barang.id', '=', 'penyesuaian_stok.barang_id')
            ->join('users', 'users.id', '=', 'penyesuaian_stok.user_id');

        if ($tanggalAwal) {
            $query->where('penyesuaian_stok.tanggal', '>=', $tanggalAwal);
        }
        if ($tanggalAkhir) {
            $query->where('penyesuaian_stok.tanggal', '<=', $tanggalAkhir);
        }

        $data = $query->orderBy('penyesuaian_stok.tanggal', 'DESC')->orderBy('penyesuaian_stok.id', 'DESC')->paginate(50);

        return view('pengaturan.penyesuaian-stok.index', [
            'title' => 'Penyesuaian Stok',
            'data' => $data,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
        ]);
    }

    public function create()
    {
        $this->requireAudit();

        $barang = Barang::orderBy('nama_barang', 'ASC')->get();

        $stokRows = DB::table('stok')
            ->join('barang', 'barang.id', '=', 'stok.barang_id')
            ->select('stok.*', 'barang.nama_barang', 'barang.kode_barang')
            ->get();

        $stokIndexed = [];
        foreach ($stokRows as $row) {
            $stokIndexed[$row->barang_id] = (array) $row;
        }

        return view('pengaturan.penyesuaian-stok.create', [
            'title' => 'Tambah Penyesuaian Stok',
            'barang' => $barang,
            'stokAll' => $stokIndexed,
        ]);
    }

    public function store(Request $request)
    {
        $this->requireAudit();

        $barangId = (int)($request->input('barang_id') ?? 0);
        $stokBaikSesudah = (int)($request->input('stok_baik_sesudah') ?? 0);
        $stokRusakSesudah = (int)($request->input('stok_rusak_sesudah') ?? 0);
        $stokSalesSesudah = (int)($request->input('stok_sales_sesudah') ?? 0);
        $alasan = trim($request->input('alasan') ?? '');
        $tanggal = $request->input('tanggal') ?? now()->format('Y-m-d');

        if ($barangId <= 0) {
            return redirect()->back()->withInput()->with('error', 'Barang wajib dipilih');
        }

        if ($stokBaikSesudah < 0 || $stokRusakSesudah < 0 || $stokSalesSesudah < 0) {
            return redirect()->back()->withInput()->with('error', 'Stok tidak boleh minus');
        }

        DB::beginTransaction();

        try {
            $stok = $this->stokService->lockStok($barangId);

            if (!$stok) {
                throw new \Exception('Stok barang tidak ditemukan');
            }

            $stokBaikSebelum = (int)$stok['stok_baik'];
            $stokRusakSebelum = (int)$stok['stok_rusak'];
            $stokSalesSebelum = (int)$stok['stok_sales'];

            $selisihBaik = $stokBaikSesudah - $stokBaikSebelum;
            $selisihRusak = $stokRusakSesudah - $stokRusakSebelum;
            $selisihSales = $stokSalesSesudah - $stokSalesSebelum;

            $penyesuaian = PenyesuaianStok::create([
                'tanggal' => $tanggal,
                'barang_id' => $barangId,
                'stok_baik_sebelum' => $stokBaikSebelum,
                'stok_baik_sesudah' => $stokBaikSesudah,
                'stok_rusak_sebelum' => $stokRusakSebelum,
                'stok_rusak_sesudah' => $stokRusakSesudah,
                'stok_sales_sebelum' => $stokSalesSebelum,
                'stok_sales_sesudah' => $stokSalesSesudah,
                'selisih_baik' => $selisihBaik,
                'selisih_rusak' => $selisihRusak,
                'selisih_sales' => $selisihSales,
                'alasan' => $alasan,
                'user_id' => auth()->id(),
            ]);

            $id = $penyesuaian->id;

            $this->stokService->updateStokById((int)$stok['id'], [
                'stok_baik' => $stokBaikSesudah,
                'stok_rusak' => $stokRusakSesudah,
                'stok_sales' => $stokSalesSesudah,
            ]);

            DB::commit();

            $this->writeAuditLog('create', $id, 'Penyesuaian stok berhasil disimpan', [
                'barang_id' => $barangId,
                'stok_baik_sebelum' => $stokBaikSebelum,
                'stok_baik_sesudah' => $stokBaikSesudah,
                'stok_rusak_sebelum' => $stokRusakSebelum,
                'stok_rusak_sesudah' => $stokRusakSesudah,
                'stok_sales_sebelum' => $stokSalesSebelum,
                'stok_sales_sesudah' => $stokSalesSesudah,
                'selisih_baik' => $selisihBaik,
                'selisih_rusak' => $selisihRusak,
                'selisih_sales' => $selisihSales,
            ]);

            $barang = Barang::find($barangId);
            $barangName = $barang ? $barang->nama_barang : 'Barang';
            $message = $barangName . ' — Baik: ' . $stokBaikSebelum . '→' . $stokBaikSesudah . ', Rusak: ' . $stokRusakSebelum . '→' . $stokRusakSesudah . ', Sales: ' . $stokSalesSebelum . '→' . $stokSalesSesudah;

            Notification::notify('Penyesuaian Stok Baru', $message, 'penyesuaian_stok', $id, ['admin', 'audit']);

            return redirect()->route('transaksi.penyesuaianstok.index')->with('success', 'Penyesuaian stok berhasil disimpan');
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
        $this->requireAudit();

        try {
            $penyesuaianStok = PenyesuaianStok::findOrFail($id);
        } catch (\Exception $e) {
            return redirect()->route('transaksi.penyesuaianstok.index')->with('error', 'Data penyesuaian stok tidak ditemukan');
        }

        $detail = PenyesuaianStok::query()
            ->select('penyesuaian_stok.*', 'barang.kode_barang', 'barang.nama_barang', 'users.nama as nama_user')
            ->join('barang', 'barang.id', '=', 'penyesuaian_stok.barang_id')
            ->join('users', 'users.id', '=', 'penyesuaian_stok.user_id')
            ->where('penyesuaian_stok.id', $penyesuaianStok->id)
            ->first();

        if (!$detail) {
            return redirect()->route('transaksi.penyesuaianstok.index')->with('error', 'Data penyesuaian stok tidak ditemukan');
        }

        return view('pengaturan.penyesuaian-stok.detail', [
            'title' => 'Detail Penyesuaian Stok',
            'detail' => $detail,
        ]);
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
