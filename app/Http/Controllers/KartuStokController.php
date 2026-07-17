<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Stok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KartuStokController extends Controller
{
    public function index(Request $request)
    {
        $barangId = $request->get('barang_id');
        $tanggalAwal = $this->normalizeDate($request->get('tanggal_awal'));
        $tanggalAkhir = $this->normalizeDate($request->get('tanggal_akhir'));

        $histori = [];
        $saldoAkhir = 0;
        $totalMasuk = 0;
        $totalKeluar = 0;
        $barangSelected = null;
        $pagination = null;

        if ($barangId) {
            $barangSelected = Barang::find($barangId);

            if (($request->has('tanggal_awal') || $request->has('tanggal_akhir')) && (!$request->filled('tanggal_awal') || !$request->filled('tanggal_akhir'))) {
                return redirect()->route('laporan.kartustok.index', ['barang_id' => $barangId])
                    ->with('error', 'Silakan pilih tanggal awal dan akhir terlebih dahulu');
            }

            $tanggalAwal = $tanggalAwal ?: date('Y-m-01');
            $tanggalAkhir = $tanggalAkhir ?: date('Y-m-d');

            if ($tanggalAwal > $tanggalAkhir) {
                [$tanggalAwal, $tanggalAkhir] = [$tanggalAkhir, $tanggalAwal];
            }

            // Use +1 day for exclusive end date
            $tglAkhir = date('Y-m-d', strtotime($tanggalAkhir.' +1 day'));

            // MySQL-compatible SQL with manual pagination
            $sql = "
                SELECT
                    sub.tanggal,
                    sub.surat_jalan,
                    sub.transaksi,
                    sub.sumber_stok,
                    sub.keterangan,
                    sub.masuk,
                    sub.keluar,
                    sub.saldo_masuk,
                    sub.saldo_keluar
                FROM (
                    (
                        SELECT
                            DATE(created_at) as tanggal,
                            '-' as surat_jalan,
                            'Initial Stok' as transaksi,
                            'Gudang' as sumber_stok,
                            '-' as keterangan,
                            (
                            COALESCE(qty_baik, 0)
                            +
                            COALESCE(qty_rusak, 0)
                            ) as masuk,
                            0 as keluar,
                            (
                            COALESCE(qty_baik, 0)
                            +
                            COALESCE(qty_rusak, 0)
                            ) as saldo_masuk,
                            0 as saldo_keluar,
                            DATE(created_at) as sort_time,
                            id as sort_id
                        FROM initialstok
                        WHERE barang_id = ? AND created_at >= ? AND created_at < ?
                    )
                    UNION ALL
                    (
                        SELECT
                            bm.tanggal,
                            bm.no_surat as surat_jalan,
                            CASE
                                WHEN bm.tipe = 'retur'
                                THEN 'Retur Toko'
                                ELSE 'Barang Masuk'
                            END as transaksi,
                            'Gudang' as sumber_stok,
                            CASE
                                WHEN bm.tipe = 'retur'
                                THEN COALESCE(t.nama_toko, '-')
                                ELSE COALESCE(p.nama_pabrik, '-')
                            END as keterangan,
                            (
                            COALESCE(bmd.qty_baik, 0)
                            +
                            COALESCE(bmd.qty_rusak, 0)
                            ) as masuk,
                            0 as keluar,
                            (
                            COALESCE(bmd.qty_baik, 0)
                            +
                            COALESCE(bmd.qty_rusak, 0)
                            ) as saldo_masuk,
                            0 as saldo_keluar,
                            bm.tanggal as sort_time,
                            bm.id as sort_id
                        FROM barang_masuk_detail bmd
                        JOIN barang_masuk bm ON bm.id = bmd.barang_masuk_id
                        LEFT JOIN toko t ON t.id = bm.toko_id
                        LEFT JOIN pabrik p ON p.id = bm.pabrik_id
                        WHERE bmd.barang_id = ? AND bm.tanggal >= ? AND bm.tanggal < ?
                    )
                    UNION ALL
                    (
                        SELECT
                            bk.tanggal,
                            bk.no_surat as surat_jalan,
                            'Barang Keluar' as transaksi,
                            CASE
                                WHEN COALESCE(bk.sumber, 'gudang') = 'sales'
                                THEN 'Sales'
                                ELSE 'Gudang'
                            END as sumber_stok,
                            CONCAT(
                                COALESCE(t.nama_toko, '-'),
                                ' | ',
                                COALESCE(sales.nama, '-'),
                                CASE
                                    WHEN bk.keterangan IS NOT NULL AND bk.keterangan != ''
                                    THEN CONCAT(' | ', bk.keterangan)
                                    ELSE ''
                                END
                            ) as keterangan,
                            0 as masuk,
                            COALESCE(bkd.qty_baik, 0) as keluar,
                            0 as saldo_masuk,
                            COALESCE(bkd.qty_baik, 0) as saldo_keluar,
                            bk.tanggal as sort_time,
                            bk.id as sort_id
                        FROM barang_keluar_detail bkd
                        JOIN barang_keluar bk ON bk.id = bkd.barang_keluar_id
                        LEFT JOIN toko t ON t.id = bk.toko_id
                        LEFT JOIN users sales ON sales.id = bk.sales_id
                        WHERE bkd.barang_id = ? AND bk.tanggal >= ? AND bk.tanggal < ?
                    )
                    UNION ALL
                    (
                        SELECT
                            m.tanggal,
                            m.no_mutasi as surat_jalan,
                            CASE
                                WHEN md.tipe IN ('baik_ke_rusak', 'rusak_ke_baik') THEN 'Mutasi Kondisi'
                                WHEN md.tipe IN ('baik_ke_sales', 'sales_ke_baik') THEN 'Mutasi Kanvas'
                            END as transaksi,
                            CASE
                                WHEN md.tipe IN ('baik_ke_sales', 'sales_ke_baik')
                                THEN 'Sales'
                                ELSE 'Gudang'
                            END as sumber_stok,
                            CASE
                                WHEN md.tipe IN ('baik_ke_rusak', 'rusak_ke_baik') THEN
                                    CASE
                                        WHEN m.keterangan IS NOT NULL AND m.keterangan != ''
                                        THEN CONCAT(
                                            CASE WHEN md.tipe = 'baik_ke_rusak' THEN 'Baik ke Rusak : '
                                                 ELSE 'Rusak ke Baik : ' END,
                                            md.qty, ' | ', m.keterangan
                                        )
                                        ELSE CONCAT(
                                            CASE WHEN md.tipe = 'baik_ke_rusak' THEN 'Baik ke Rusak : '
                                                 ELSE 'Rusak ke Baik : ' END,
                                            md.qty
                                        )
                                    END
                                WHEN md.tipe IN ('baik_ke_sales', 'sales_ke_baik') THEN
                                    CASE
                                        WHEN m.keterangan IS NOT NULL AND m.keterangan != ''
                                        THEN CONCAT(
                                            CASE WHEN md.tipe = 'baik_ke_sales' THEN 'Berangkat Kanvas : '
                                                 ELSE 'Sisa Kanvas : ' END,
                                            md.qty, ' | ',
                                            COALESCE(sales_mutasi.nama, '-'),
                                            ' | ', m.keterangan
                                        )
                                        ELSE CONCAT(
                                            CASE WHEN md.tipe = 'baik_ke_sales' THEN 'Berangkat Kanvas : '
                                                 ELSE 'Sisa Kanvas : ' END,
                                            md.qty, ' | ',
                                            COALESCE(sales_mutasi.nama, '-')
                                        )
                                    END
                                ELSE COALESCE(m.keterangan, '-')
                            END as keterangan,
                            0 as masuk,
                            0 as keluar,
                            0 as saldo_masuk,
                            0 as saldo_keluar,
                            m.tanggal as sort_time,
                            m.id as sort_id
                        FROM mutasi_detail md
                        JOIN mutasi m ON m.id = md.mutasi_id
                        LEFT JOIN users sales_mutasi ON sales_mutasi.id = m.sales_id
                        WHERE md.barang_id = ? AND m.tanggal >= ? AND m.tanggal < ?
                    )
                    UNION ALL
                    (
                        SELECT
                            ps.tanggal,
                            CONCAT('PS-', ps.id) as surat_jalan,
                            CASE WHEN ps.alasan LIKE 'Stok Opname:%' THEN 'Stok Opname' ELSE 'Penyesuaian Stok' END as transaksi,
                            'Semua' as sumber_stok,
                            COALESCE(ps.alasan, '-') as keterangan,
                            CASE
                                WHEN (COALESCE(ps.selisih_baik, 0) + COALESCE(ps.selisih_rusak, 0) + COALESCE(ps.selisih_sales, 0)) > 0
                                THEN (COALESCE(ps.selisih_baik, 0) + COALESCE(ps.selisih_rusak, 0) + COALESCE(ps.selisih_sales, 0))
                                ELSE 0
                            END as masuk,
                            CASE
                                WHEN (COALESCE(ps.selisih_baik, 0) + COALESCE(ps.selisih_rusak, 0) + COALESCE(ps.selisih_sales, 0)) < 0
                                THEN ABS(COALESCE(ps.selisih_baik, 0) + COALESCE(ps.selisih_rusak, 0) + COALESCE(ps.selisih_sales, 0))
                                ELSE 0
                            END as keluar,
                            CASE
                                WHEN (COALESCE(ps.selisih_baik, 0) + COALESCE(ps.selisih_rusak, 0) + COALESCE(ps.selisih_sales, 0)) > 0
                                THEN (COALESCE(ps.selisih_baik, 0) + COALESCE(ps.selisih_rusak, 0) + COALESCE(ps.selisih_sales, 0))
                                ELSE 0
                            END as saldo_masuk,
                            CASE
                                WHEN (COALESCE(ps.selisih_baik, 0) + COALESCE(ps.selisih_rusak, 0) + COALESCE(ps.selisih_sales, 0)) < 0
                                THEN ABS(COALESCE(ps.selisih_baik, 0) + COALESCE(ps.selisih_rusak, 0) + COALESCE(ps.selisih_sales, 0))
                                ELSE 0
                            END as saldo_keluar,
                            ps.tanggal as sort_time,
                            ps.id as sort_id
                        FROM penyesuaian_stok ps
                        WHERE ps.barang_id = ? AND ps.tanggal >= ? AND ps.tanggal < ?
                    )
                ) sub
                ORDER BY sub.sort_time ASC, sub.sort_id ASC
            ";

            $initParams = [$barangId, $tanggalAwal, $tglAkhir];
            $masukParams = [$barangId, $tanggalAwal, $tglAkhir];
            $keluarParams = [$barangId, $tanggalAwal, $tglAkhir];
            $mutasiParams = [$barangId, $tanggalAwal, $tglAkhir];
            $penyesuaianParams = [$barangId, $tanggalAwal, $tglAkhir];

            $params = array_merge($initParams, $masukParams, $keluarParams, $mutasiParams, $penyesuaianParams);

            $page = (int) ($request->get('page') ?? 1);
            $perPage = 50;
            $offset = ($page - 1) * $perPage;

            // First, get total count
            $countSql = "SELECT COUNT(*) as total FROM ({$sql}) as count_table";
            $countResult = DB::selectOne($countSql, $params);
            $totalCount = (int) ($countResult->total ?? 0);

            // Now paginate
            $sql .= ' LIMIT '.(int) $perPage.' OFFSET '.(int) $offset;

            $result = DB::select($sql, $params);

            $histori = array_map(function ($row) {
                return (array) $row;
            }, $result);

            $totalPages = max(1, (int) ceil($totalCount / $perPage));

            if ($page > $totalPages) {
                $page = $totalPages;
            }

            $pagination = [
                'page' => $page,
                'perPage' => $perPage,
                'total' => $totalCount,
                'totalPages' => $totalPages,
            ];

            // Sum SQL
            $sumSql = '
                SELECT
                    COALESCE(SUM(masuk), 0) as total_masuk,
                    COALESCE(SUM(keluar), 0) as total_keluar
                FROM (
                    (
                        SELECT
                            COALESCE(qty_baik, 0)
                            +
                            COALESCE(qty_rusak, 0) as masuk,
                            0 as keluar
                        FROM initialstok
                        WHERE barang_id = ? AND created_at >= ? AND created_at < ?
                    )
                    UNION ALL
                    (
                        SELECT
                            COALESCE(bmd.qty_baik, 0)
                            +
                            COALESCE(bmd.qty_rusak, 0) as masuk,
                            0 as keluar
                        FROM barang_masuk_detail bmd
                        JOIN barang_masuk bm ON bm.id = bmd.barang_masuk_id
                        WHERE bmd.barang_id = ? AND bm.tanggal >= ? AND bm.tanggal < ?
                    )
                    UNION ALL
                    (
                        SELECT
                            0 as masuk,
                            COALESCE(bkd.qty_baik, 0) as keluar
                        FROM barang_keluar_detail bkd
                        JOIN barang_keluar bk ON bk.id = bkd.barang_keluar_id
                        WHERE bkd.barang_id = ? AND bk.tanggal >= ? AND bk.tanggal < ?
                    )
                    UNION ALL
                    (
                        SELECT
                            0 as masuk,
                            0 as keluar
                        FROM mutasi_detail md
                        JOIN mutasi m ON m.id = md.mutasi_id
                        WHERE md.barang_id = ? AND m.tanggal >= ? AND m.tanggal < ?
                    )
                    UNION ALL
                    (
                        SELECT
                            CASE
                                WHEN (COALESCE(ps.selisih_baik, 0) + COALESCE(ps.selisih_rusak, 0) + COALESCE(ps.selisih_sales, 0)) > 0
                                THEN (COALESCE(ps.selisih_baik, 0) + COALESCE(ps.selisih_rusak, 0) + COALESCE(ps.selisih_sales, 0))
                                ELSE 0
                            END as masuk,
                            CASE
                                WHEN (COALESCE(ps.selisih_baik, 0) + COALESCE(ps.selisih_rusak, 0) + COALESCE(ps.selisih_sales, 0)) < 0
                                THEN ABS(COALESCE(ps.selisih_baik, 0) + COALESCE(ps.selisih_rusak, 0) + COALESCE(ps.selisih_sales, 0))
                                ELSE 0
                            END as keluar
                        FROM penyesuaian_stok ps
                        WHERE ps.barang_id = ? AND ps.tanggal >= ? AND ps.tanggal < ?
                    )
                ) sub
            ';

            $totals = DB::selectOne($sumSql, $params);

            $totalMasuk = (int) ($totals->total_masuk ?? 0);
            $totalKeluar = (int) ($totals->total_keluar ?? 0);

            $saldoAwalSql = '
                SELECT
                    COALESCE((
                        SELECT COALESCE(SUM(qty_baik + qty_rusak), 0)
                        FROM initialstok
                        WHERE barang_id = ? AND created_at < ?
                    ), 0) +
                    COALESCE((
                        SELECT COALESCE(SUM(bmd.qty_baik + bmd.qty_rusak), 0)
                        FROM barang_masuk_detail bmd
                        JOIN barang_masuk bm ON bm.id = bmd.barang_masuk_id
                        WHERE bmd.barang_id = ? AND bm.tanggal < ?
                    ), 0) -
                    COALESCE((
                        SELECT COALESCE(SUM(bkd.qty_baik), 0)
                        FROM barang_keluar_detail bkd
                        JOIN barang_keluar bk ON bk.id = bkd.barang_keluar_id
                        WHERE bkd.barang_id = ? AND bk.tanggal < ?
                    ), 0) +
                    COALESCE((
                        SELECT COALESCE(SUM(selisih_baik + selisih_rusak + selisih_sales), 0)
                        FROM penyesuaian_stok
                        WHERE barang_id = ? AND tanggal < ?
                    ), 0) as saldo_awal
            ';

            $saldoAwalResult = DB::selectOne($saldoAwalSql, [
                $barangId, $tanggalAwal,
                $barangId, $tanggalAwal,
                $barangId, $tanggalAwal,
                $barangId, $tanggalAwal,
            ]);

            $saldoAwal = (int) ($saldoAwalResult->saldo_awal ?? 0);

            $stok = Stok::where('barang_id', $barangId)->first();

            if ($stok) {
                $saldoAkhir = (int) $stok->stok_baik + (int) $stok->stok_rusak + (int) $stok->stok_sales;
                $stokBaik = (int) $stok->stok_baik;
                $stokRusak = (int) $stok->stok_rusak;
                $stokSales = (int) $stok->stok_sales;
            } else {
                $stokBaik = 0;
                $stokRusak = 0;
                $stokSales = 0;
            }
        } else {
            $stokBaik = 0;
            $stokRusak = 0;
            $stokSales = 0;
        }

        $data = [
            'title' => 'Kartu Stok',
            'barang' => Barang::orderBy('nama_barang', 'ASC')->get(),
            'barangSelected' => $barangSelected,
            'histori' => $histori,
            'pagination' => $pagination,
            'saldoAwal' => $saldoAwal ?? 0,
            'saldoAkhir' => $saldoAkhir,
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'stokBaik' => $stokBaik,
            'stokRusak' => $stokRusak,
            'stokSales' => $stokSales,
        ];

        return view('laporan.kartu-stok', $data);
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
