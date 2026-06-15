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

            $tanggalAwal = $tanggalAwal ?: date('Y-m-d', strtotime('-1 month'));
            $tanggalAkhir = $tanggalAkhir ?: date('Y-m-d');

            if ($tanggalAwal > $tanggalAkhir) {
                [$tanggalAwal, $tanggalAkhir] = [$tanggalAkhir, $tanggalAwal];
            }

            // Use +1 day for exclusive end date
            $tglAkhir = date('Y-m-d', strtotime($tanggalAkhir . ' +1 day'));

            // MySQL-compatible SQL with manual pagination
            $sql = "
                SELECT
                    sub.tanggal,
                    sub.surat_jalan,
                    sub.transaksi,
                    sub.keterangan,
                    sub.masuk,
                    sub.keluar
                FROM (
                    (
                        SELECT
                            DATE(created_at) as tanggal,
                            '-' as surat_jalan,
                            'Initial Stok' as transaksi,
                            '-' as keterangan,
                            (
                                COALESCE(qty_baik, 0)
                                +
                                COALESCE(qty_rusak, 0)
                            ) as masuk,
                            0 as keluar
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
                            0 as keluar
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
                            CONCAT(
                                COALESCE(t.nama_toko, '-'),
                                ' / ',
                                COALESCE(sales.nama, '-')
                            ) as keterangan,
                            0 as masuk,
                            COALESCE(bkd.qty_baik, 0) as keluar
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
                                WHEN md.tipe = 'baik_ke_rusak'
                                THEN 'Mutasi Baik ke Rusak'
                                ELSE 'Mutasi Rusak ke Baik'
                            END as transaksi,
                            COALESCE(m.keterangan, '-') as keterangan,
                            0 as masuk,
                            0 as keluar
                        FROM mutasi_detail md
                        JOIN mutasi m ON m.id = md.mutasi_id
                        WHERE md.barang_id = ? AND m.tanggal >= ? AND m.tanggal < ?
                    )
                    UNION ALL
                    (
                        SELECT
                            ps.tanggal,
                            CONCAT('PS-', ps.id) as surat_jalan,
                            'Penyesuaian Stok' as transaksi,
                            COALESCE(ps.alasan, '-') as keterangan,
                            CASE
                                WHEN (COALESCE(ps.selisih_baik, 0) + COALESCE(ps.selisih_rusak, 0)) > 0
                                THEN (COALESCE(ps.selisih_baik, 0) + COALESCE(ps.selisih_rusak, 0))
                                ELSE 0
                            END as masuk,
                            CASE
                                WHEN (COALESCE(ps.selisih_baik, 0) + COALESCE(ps.selisih_rusak, 0)) < 0
                                THEN ABS(COALESCE(ps.selisih_baik, 0) + COALESCE(ps.selisih_rusak, 0))
                                ELSE 0
                            END as keluar
                        FROM penyesuaian_stok ps
                        WHERE ps.barang_id = ? AND ps.tanggal >= ? AND ps.tanggal < ?
                    )
                ) sub
                ORDER BY sub.tanggal ASC
            ";

            $initParams = [$barangId, $tanggalAwal, $tglAkhir];
            $masukParams = [$barangId, $tanggalAwal, $tglAkhir];
            $keluarParams = [$barangId, $tanggalAwal, $tglAkhir];
            $mutasiParams = [$barangId, $tanggalAwal, $tglAkhir];
            $penyesuaianParams = [$barangId, $tanggalAwal, $tglAkhir];

            $params = array_merge($initParams, $masukParams, $keluarParams, $mutasiParams, $penyesuaianParams);

            $page = (int)($request->get('page') ?? 1);
            $perPage = 50;
            $offset = ($page - 1) * $perPage;

            // First, get total count
            $countSql = "SELECT COUNT(*) as total FROM ({$sql}) as count_table";
            $countResult = DB::selectOne($countSql, $params);
            $totalCount = (int)($countResult->total ?? 0);

            // Now paginate
            $sql .= " LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;

            $result = DB::select($sql, $params);

            $histori = array_map(function ($row) {
                return (array) $row;
            }, $result);

            $totalPages = max(1, (int)ceil($totalCount / $perPage));

            if ($page > $totalPages) $page = $totalPages;

            $pagination = [
                'page' => $page,
                'perPage' => $perPage,
                'total' => $totalCount,
                'totalPages' => $totalPages,
            ];

            // Sum SQL
            $sumSql = "
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
                                WHEN (COALESCE(ps.selisih_baik, 0) + COALESCE(ps.selisih_rusak, 0)) > 0
                                THEN (COALESCE(ps.selisih_baik, 0) + COALESCE(ps.selisih_rusak, 0))
                                ELSE 0
                            END as masuk,
                            CASE
                                WHEN (COALESCE(ps.selisih_baik, 0) + COALESCE(ps.selisih_rusak, 0)) < 0
                                THEN ABS(COALESCE(ps.selisih_baik, 0) + COALESCE(ps.selisih_rusak, 0))
                                ELSE 0
                            END as keluar
                        FROM penyesuaian_stok ps
                        WHERE ps.barang_id = ? AND ps.tanggal >= ? AND ps.tanggal < ?
                    )
                ) sub
            ";

            $totals = DB::selectOne($sumSql, $params);

            $totalMasuk = (int)($totals->total_masuk ?? 0);
            $totalKeluar = (int)($totals->total_keluar ?? 0);

            $stok = Stok::where('barang_id', $barangId)->first();

            if ($stok) {
                $saldoAkhir = (int)$stok->stok_baik + (int)$stok->stok_rusak;
                $stokBaik = (int)$stok->stok_baik;
                $stokRusak = (int)$stok->stok_rusak;
            } else {
                $stokBaik = 0;
                $stokRusak = 0;
            }
        } else {
            $stokBaik = 0;
            $stokRusak = 0;
        }

        $data = [
            'title' => 'Kartu Stok',
            'barang' => Barang::orderBy('nama_barang', 'ASC')->get(),
            'barangSelected' => $barangSelected,
            'histori' => $histori,
            'pagination' => $pagination,
            'saldoAkhir' => $saldoAkhir,
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'stokBaik' => $stokBaik,
            'stokRusak' => $stokRusak,
        ];

        return view('laporan.kartu-stok', $data);
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
