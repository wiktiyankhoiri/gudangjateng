<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanBarangMasukController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->get('keyword');
        $tanggalAwal = $this->normalizeDate($request->get('tanggal_awal'));
        $tanggalAkhir = $this->normalizeDate($request->get('tanggal_akhir'));
        $tipe = $request->get('tipe');

        $query = BarangMasuk::query()
            ->selectRaw("barang_masuk.*, CASE WHEN tipe = 'pabrik' THEN pabrik.nama_pabrik ELSE toko.nama_toko END as relasi")
            ->leftJoin('pabrik', 'pabrik.id', '=', 'barang_masuk.pabrik_id')
            ->leftJoin('toko', 'toko.id', '=', 'barang_masuk.toko_id');

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('no_surat', 'like', "%{$keyword}%")
                  ->orWhere('pabrik.nama_pabrik', 'like', "%{$keyword}%")
                  ->orWhere('toko.nama_toko', 'like', "%{$keyword}%");
            });
        }

        if ($tipe) {
            $query->where('tipe', $tipe);
        }

        if ($tanggalAwal) {
            $query->where('tanggal', '>=', $tanggalAwal);
        }

        if ($tanggalAkhir) {
            $query->where('tanggal', '<=', $tanggalAkhir);
        }

        $data = $query->orderBy('tanggal', 'DESC')->paginate(50);

        return view('laporan.laporan-barang-masuk', [
            'title' => 'Laporan Barang Masuk',
            'data' => $data,
            'keyword' => $keyword,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
            'tipe' => $tipe,
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
