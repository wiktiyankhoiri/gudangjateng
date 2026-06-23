<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->get('q');
        if (!$keyword || strlen(trim($keyword)) < 2) {
            return response()->json(['barang' => [], 'transaksi' => []]);
        }

        $keyword = trim($keyword);
        $escLow = strtolower($keyword);
        $searchTerm = str_replace(' ', '', $escLow);

        $barang = DB::table('barang as b')
            ->select('b.id', 'b.kode_barang', 'b.nama_barang', 's.stok_baik', 's.stok_rusak')
            ->leftJoin('stok as s', 's.barang_id', '=', 'b.id')
            ->where(function ($q) use ($searchTerm) {
                $q->whereRaw('REPLACE(b.kode_barang, \' \', \'\') ILIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('REPLACE(b.nama_barang, \' \', \'\') ILIKE ?', ['%' . $searchTerm . '%']);
            })
            ->orderBy('b.kode_barang', 'ASC')
            ->limit(5)
            ->get();

        $barangMasuk = DB::table('barang_masuk as bm')
            ->selectRaw("bm.id, bm.no_surat, bm.tanggal, 'barang_masuk' as tipe")
            ->whereRaw('bm.no_surat ILIKE ?', ['%' . $escLow . '%'])
            ->orderBy('bm.id', 'DESC')
            ->limit(5)
            ->get();

        $barangKeluar = DB::table('barang_keluar as bk')
            ->selectRaw("bk.id, bk.no_surat, bk.tanggal, 'barang_keluar' as tipe")
            ->whereRaw('bk.no_surat ILIKE ?', ['%' . $escLow . '%'])
            ->orderBy('bk.id', 'DESC')
            ->limit(5)
            ->get();

        $transaksi = array_merge($barangMasuk->toArray(), $barangKeluar->toArray());

        // Mutasi: hanya untuk role yang memiliki akses (exclude sales)
        $role = auth()->user()->role;
        if (!in_array($role, ['sales', 'staff'], true)) {
            $mutasi = DB::table('mutasi as m')
                ->selectRaw("m.id, m.no_mutasi as no_surat, m.tanggal, 'mutasi' as tipe")
                ->whereRaw('m.no_mutasi ILIKE ?', ['%' . $escLow . '%'])
                ->orderBy('m.id', 'DESC')
                ->limit(5)
                ->get();

            $transaksi = array_merge($transaksi, $mutasi->toArray());
        }

        return response()->json([
            'barang' => $barang,
            'transaksi' => $transaksi,
        ]);
    }
}
