<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanBarangKeluarController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->get('keyword');
        $tanggalAwal = $this->normalizeDate($request->get('tanggal_awal'));
        $tanggalAkhir = $this->normalizeDate($request->get('tanggal_akhir'));
        $salesId = $request->get('sales_id');

        $query = BarangKeluar::query()
            ->leftJoin('toko', 'toko.id', '=', 'barang_keluar.toko_id')
            ->leftJoin('users as sales', 'sales.id', '=', 'barang_keluar.sales_id')
            ->selectRaw('barang_keluar.*, toko.nama_toko, sales.nama as nama_sales');

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('no_surat', 'like', "%{$keyword}%")
                  ->orWhere('toko.nama_toko', 'like', "%{$keyword}%")
                  ->orWhere('sales.nama', 'like', "%{$keyword}%");
            });
        }

        if ($salesId) {
            $query->where('barang_keluar.sales_id', $salesId);
        }

        if ($tanggalAwal) {
            $query->where('barang_keluar.tanggal', '>=', $tanggalAwal);
        }

        if ($tanggalAkhir) {
            $query->where('barang_keluar.tanggal', '<=', $tanggalAkhir);
        }

        $data = $query->orderBy('barang_keluar.tanggal', 'DESC')->orderBy('barang_keluar.id', 'DESC')->paginate(50);

        $salesList = User::where('role', 'sales')->orderBy('nama', 'ASC')->get();

        return view('laporan.laporan-barang-keluar', [
            'title' => 'Laporan Barang Keluar',
            'data' => $data,
            'keyword' => $keyword,
            'salesId' => $salesId,
            'salesList' => $salesList,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
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
