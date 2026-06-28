<?php

namespace App\Http\Controllers;

use App\Models\Mutasi;
use Illuminate\Http\Request;

class LaporanMutasiController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->get('keyword');
        $tanggalAwal = $this->normalizeDate($request->get('tanggal_awal'));
        $tanggalAkhir = $this->normalizeDate($request->get('tanggal_akhir'));

        $query = Mutasi::query();

        if ($keyword) {
            $query->where('no_mutasi', 'like', "%{$keyword}%");
        }

        if ($tanggalAwal) {
            $query->where('tanggal', '>=', $tanggalAwal);
        }

        if ($tanggalAkhir) {
            $query->where('tanggal', '<=', $tanggalAkhir);
        }

        $data = $query->orderBy('tanggal', 'DESC')->orderBy('id', 'DESC')->paginate(50);

        return view('laporan.laporan-mutasi', [
            'title' => 'Laporan Mutasi',
            'data' => $data,
            'keyword' => $keyword,
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
