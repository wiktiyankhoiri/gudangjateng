<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\BarangMasukDetail;
use App\Models\BarangKeluarDetail;
use App\Models\Mutasi;
use App\Models\Stok;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\StokOpname;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // KPI Global
        $totalBarang = Barang::count();

        $stok = Stok::selectRaw('COALESCE(SUM(stok_baik), 0) as stok_baik, COALESCE(SUM(stok_rusak), 0) as stok_rusak')->first();
        $totalStokBaik = (int) ($stok->stok_baik ?? 0);
        $totalStokRusak = (int) ($stok->stok_rusak ?? 0);

        $totalBarangMasuk = BarangMasuk::count();
        $totalBarangKeluar = BarangKeluar::count();

        // Monthly chart data
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');

        $chartMasuk = BarangMasuk::selectRaw('DATE(tanggal) as tgl, COUNT(*) as total')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->groupBy('tgl')
            ->orderBy('tgl', 'ASC')
            ->get()
            ->toArray();

        $chartKeluar = BarangKeluar::selectRaw('DATE(tanggal) as tgl, COUNT(*) as total')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->groupBy('tgl')
            ->orderBy('tgl', 'ASC')
            ->get()
            ->toArray();

        $totalMasukBulanIni = array_sum(array_column($chartMasuk, 'total'));
        $totalKeluarBulanIni = array_sum(array_column($chartKeluar, 'total'));

        $masukBulanLalu = BarangMasuk::whereBetween('tanggal', [
            date('Y-m-01', strtotime('-1 month')),
            date('Y-m-t', strtotime('-1 month'))
        ])->count();

        $keluarBulanLalu = BarangKeluar::whereBetween('tanggal', [
            date('Y-m-01', strtotime('-1 month')),
            date('Y-m-t', strtotime('-1 month'))
        ])->count();

        $persenMasuk = $masukBulanLalu > 0 ? (($totalMasukBulanIni - $masukBulanLalu) / $masukBulanLalu * 100) : 0;
        $persenKeluar = $keluarBulanLalu > 0 ? (($totalKeluarBulanIni - $keluarBulanLalu) / $keluarBulanLalu * 100) : 0;

        // Shared queries
        $today = date('Y-m-d');
        $barangMasukHariIni = BarangMasuk::where('tanggal', $today)->count();
        $barangKeluarHariIni = BarangKeluar::where('tanggal', $today)->count();
        $mutasiHariIni = Mutasi::whereDate('created_at', $today)->count();

        $barangKeluarTerbaru = BarangKeluar::select('barang_keluar.*', 'toko.nama_toko', 'users.nama as nama_sales')
            ->leftJoin('toko', 'toko.id', '=', 'barang_keluar.toko_id')
            ->leftJoin('users', 'users.id', '=', 'barang_keluar.sales_id')
            ->withCount('details')
            ->orderBy('barang_keluar.tanggal', 'DESC')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $arr = $item->toArray();
                $arr['total_item'] = $arr['details_count'] ?? 0;
                return $arr;
            })
            ->toArray();

        // Admin: Barang Masuk Terbaru
        $adminBarangMasukTerbaru = BarangMasuk::select('barang_masuk.*', 'pabrik.nama_pabrik', 'toko.nama_toko')
            ->leftJoin('pabrik', 'pabrik.id', '=', 'barang_masuk.pabrik_id')
            ->leftJoin('toko', 'toko.id', '=', 'barang_masuk.toko_id')
            ->withCount('details')
            ->orderBy('barang_masuk.tanggal', 'DESC')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $arr = $item->toArray();
                $arr['total_item'] = $arr['details_count'] ?? 0;
                return $arr;
            })
            ->toArray();

        // Sales: Qty Barang Keluar Hari Ini
        $salesQtyBarangKeluarHariIni = BarangKeluarDetail::join('barang_keluar', 'barang_keluar.id', '=', 'barang_keluar_detail.barang_keluar_id')
            ->whereDate('barang_keluar.tanggal', $today)
            ->selectRaw('COALESCE(SUM(qty_baik), 0) + COALESCE(SUM(qty_rusak), 0) as total')
            ->first()
            ->total;

        // Admin specific
        $user = auth()->user();
        $userAktif = User::count();
        $totalMutasi = Mutasi::count();
        $totalRetur = BarangMasuk::where('tipe', 'retur')->count();
        $totalAuditLog = AuditLog::count();

        $backupPath = storage_path('app/backups');
        $totalBackup = 0;
        if (is_dir($backupPath)) {
            $totalBackup = count(glob($backupPath . '/*.sql'));
        }

        // Admin: Retur Terbaru
        $adminReturTerbaru = BarangMasuk::select('barang_masuk.id', 'barang_masuk.no_surat', 'barang_masuk.tanggal', 'barang_masuk.tipe', 'barang_masuk.keterangan', 'toko.nama_toko')
            ->selectRaw('COUNT(barang_masuk_detail.id) as total_item')
            ->leftJoin('barang_masuk_detail', 'barang_masuk_detail.barang_masuk_id', '=', 'barang_masuk.id')
            ->leftJoin('toko', 'toko.id', '=', 'barang_masuk.toko_id')
            ->where('barang_masuk.tipe', 'retur')
            ->groupBy('barang_masuk.id', 'barang_masuk.no_surat', 'barang_masuk.tanggal', 'barang_masuk.tipe', 'barang_masuk.keterangan', 'toko.nama_toko')
            ->orderBy('barang_masuk.tanggal', 'DESC')
            ->limit(5)
            ->get()
            ->toArray();

        // Admin: Mutasi Terbaru
        $adminMutasiTerbaruList = DB::select("
            SELECT mutasi.id as mutasi_id, mutasi.no_mutasi, mutasi.keterangan as alasan,
                md.tipe, md.qty
            FROM mutasi_detail md
            LEFT JOIN mutasi ON mutasi.id = md.mutasi_id
            ORDER BY mutasi.tanggal DESC
            LIMIT 10
        ");
        $mutasiList = [];
        foreach ($adminMutasiTerbaruList as $row) {
            $tipe = strtolower(trim($row->tipe ?? ''));
            $qty = (int) ($row->qty ?? 0);
            $mutasiList[] = [
                'mutasi_id' => $row->mutasi_id,
                'no_mutasi' => $row->no_mutasi ?? '-',
                'alasan' => $row->alasan ?? '-',
                'tipe' => $tipe === 'rusak_ke_baik' ? 'Rusak → Baik' : 'Baik → Rusak',
                'total_selisih' => $tipe === 'rusak_ke_baik' ? $qty : ($qty * -1),
            ];
        }

        // Audit: Mutasi Terbaru
        $auditPenyesuaianTerbaru = array_slice($mutasiList, 0, 5);
        $totalSelisihItem = array_sum(array_column($mutasiList, 'total_selisih'));

        $stokChart = DB::table('stok as s')
            ->select('barang.nama_barang', 's.stok_baik', 's.stok_rusak')
            ->join('barang', 'barang.id', '=', 's.barang_id')
            ->where(function ($q) {
                $q->where('s.stok_baik', '>', 0)
                  ->orWhere('s.stok_rusak', '>', 0);
            })
            ->orderBy('s.stok_baik', 'DESC')
            ->limit(10)
            ->get()
            ->toArray();

        $stokMenipis = DB::table('stok as s')
            ->select('barang.kode_barang', 'barang.nama_barang', 's.stok_baik', 's.stok_rusak')
            ->leftJoin('barang', 'barang.id', '=', 's.barang_id')
            ->where('s.stok_baik', '>', 0)
            ->where('s.stok_baik', '<=', 5)
            ->orderBy('s.stok_baik', 'ASC')
            ->orderBy('barang.nama_barang', 'ASC')
            ->limit(5)
            ->get()
            ->map(fn($item) => (array) $item)
            ->toArray();

        $transaksiGagal = AuditLog::where(function ($q) {
            $q->where('action', 'update_gagal')
              ->orWhere('action', 'delete_gagal');
        })->whereDate('created_at', $today)->count();

        $auditLogTerbaru = AuditLog::select('audit_log.*', 'users.nama as user_nama')
            ->leftJoin('users', 'users.id', '=', 'audit_log.user_id')
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->get();

        $barangMasuk30Hari = BarangMasuk::where('tanggal', '>=', date('Y-m-d', strtotime('-30 days')))->count();
        $barangKeluar30Hari = BarangKeluar::where('tanggal', '>=', date('Y-m-d', strtotime('-30 days')))->count();
        $stokHabisCount = Stok::where('stok_baik', 0)->count();

        // Fast / Slow Moving Items
        $fastMovingItems = BarangKeluarDetail::select('barang.nama_barang', DB::raw('SUM(barang_keluar_detail.qty_baik) as total_keluar'))
            ->join('barang', 'barang.id', '=', 'barang_keluar_detail.barang_id')
            ->join('barang_keluar', 'barang_keluar.id', '=', 'barang_keluar_detail.barang_keluar_id')
            ->where('barang_keluar.tanggal', '>=', date('Y-m-d', strtotime('-30 days')))
            ->groupBy('barang.id', 'barang.nama_barang')
            ->orderBy('total_keluar', 'DESC')
            ->limit(5)
            ->get()
            ->toArray();

        $slowMovingItems = DB::select("
            SELECT barang.nama_barang, s.stok_baik FROM stok s
            JOIN barang ON barang.id = s.barang_id
            WHERE s.stok_baik > 0
            AND (SELECT COALESCE(SUM(bkd.qty_baik), 0) FROM barang_keluar_detail bkd
                JOIN barang_keluar bk ON bk.id = bkd.barang_keluar_id
                WHERE bkd.barang_id = s.barang_id AND bk.tanggal >= CURRENT_DATE - INTERVAL '60 days') = 0
            ORDER BY s.stok_baik ASC LIMIT 5
        ");

        return view('dashboard.index', [
            'title' => 'Beranda',
            'totalBarang' => $totalBarang,
            'totalStokBaik' => $totalStokBaik,
            'totalStokRusak' => $totalStokRusak,
            'totalBarangMasuk' => $totalBarangMasuk,
            'totalBarangKeluar' => $totalBarangKeluar,
            'chartMasuk' => $chartMasuk,
            'chartKeluar' => $chartKeluar,
            'totalMasukBulanIni' => $totalMasukBulanIni,
            'totalKeluarBulanIni' => $totalKeluarBulanIni,
            'masukBulanLalu' => $masukBulanLalu,
            'keluarBulanLalu' => $keluarBulanLalu,
            'persenMasuk' => $persenMasuk,
            'persenKeluar' => $persenKeluar,
            'totalStok' => $totalStokBaik + $totalStokRusak,
            'barangMasukHariIni' => $barangMasukHariIni,
            'barangKeluarHariIni' => $barangKeluarHariIni,
            'mutasiHariIni' => $mutasiHariIni,
            'barangKeluarTerbaru' => $barangKeluarTerbaru,
            'adminBarangMasukTerbaru' => $adminBarangMasukTerbaru,
            'adminReturTerbaru' => $adminReturTerbaru,
            'adminMutasiTerbaruList' => $mutasiList,
            'auditPenyesuaianTerbaru' => $auditPenyesuaianTerbaru,
            'salesQtyBarangKeluarHariIni' => (int) $salesQtyBarangKeluarHariIni,
            'stokChart' => $stokChart,
            'userAktif' => $userAktif,
            'totalMutasi' => $totalMutasi,
            'totalRetur' => $totalRetur,
            'totalAuditLog' => $totalAuditLog,
            'totalBackup' => $totalBackup,
            'transaksiGagal' => $transaksiGagal,
            'auditLogTerbaru' => $auditLogTerbaru,
            'barangMasuk30Hari' => $barangMasuk30Hari,
            'barangKeluar30Hari' => $barangKeluar30Hari,
            'stokHabisCount' => $stokHabisCount,
            'stokMenipis' => $stokMenipis,
            'fastMovingItems' => $fastMovingItems,
            'slowMovingItems' => $slowMovingItems,
            'totalSelisihItem' => $totalSelisihItem,
            'stokOpnameCount' => [
                'draft' => StokOpname::where('status', 'draft')
                    ->whereRaw("to_char(tanggal_opname, 'YYYY-MM') = ?", [date('Y-m')])->count(),
                'selesai' => StokOpname::where('status', 'selesai')
                    ->whereRaw("to_char(tanggal_opname, 'YYYY-MM') = ?", [date('Y-m')])->count(),
                'diterapkan' => StokOpname::where('status', 'diterapkan')
                    ->whereRaw("to_char(tanggal_opname, 'YYYY-MM') = ?", [date('Y-m')])->count(),
            ],
        ]);
    }
}
