<?php

namespace App\Http\Controllers;

use App\Exceptions\BusinessException;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use App\Models\Notification;
use App\Models\Pabrik;
use App\Models\Toko;
use App\Services\StokService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangMasukController extends Controller
{
    public function __construct(
        protected StokService $stokService
    ) {}

    public function index(Request $request)
    {
        $this->requireAdmin();

        $q = $request->get('q');

        $query = BarangMasuk::query()
            ->select('barang_masuk.*')
            ->selectRaw('COALESCE(pabrik.nama_pabrik, toko.nama_toko, \'-\') as sumber')
            ->selectSub(function ($q) {
                $q->from('barang_masuk_detail')
                    ->whereColumn('barang_masuk_id', 'barang_masuk.id')
                    ->selectRaw('COUNT(*)');
            }, 'total_item')
            ->leftJoin('pabrik', 'pabrik.id', '=', 'barang_masuk.pabrik_id')
            ->leftJoin('toko', 'toko.id', '=', 'barang_masuk.toko_id');

        if ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('barang_masuk.no_surat', 'ILIKE', "%{$q}%")
                    ->orWhere('barang_masuk.keterangan', 'ILIKE', "%{$q}%")
                    ->orWhere('barang_masuk.tipe', 'ILIKE', "%{$q}%")
                    ->orWhere('pabrik.nama_pabrik', 'ILIKE', "%{$q}%")
                    ->orWhere('toko.nama_toko', 'ILIKE', "%{$q}%");
            });
        }

        $data = $query->orderBy('barang_masuk.id', 'DESC')->paginate(50);

        return view('transaksi.barang-masuk.index', [
            'title' => 'Barang Masuk',
            'data' => $data,
            'q' => $q,
        ]);
    }

    public function create()
    {
        $this->requireAdmin();

        return view('transaksi.barang-masuk.create', [
            'title' => 'Tambah Barang Masuk',
            'barang' => Barang::orderBy('nama_barang', 'ASC')->get(),
            'pabrik' => Pabrik::orderBy('nama_pabrik', 'ASC')->get(),
            'toko' => Toko::orderBy('nama_toko', 'ASC')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->requireAdmin();
        $post = $request->all();

        if (empty($post['barang_id'])) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Detail barang wajib diisi')
                ->with('field_errors', ['barang_id[]' => 'Detail barang wajib diisi']);
        }

        try {
            $detailBarang = $this->buildDetailBarang($post);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $this->getSafeErrorMessage($e));
        }

        DB::beginTransaction();

        try {
            $existing = BarangMasuk::where('no_surat', $post['no_surat'])->first();

            if ($existing) {
                throw new BusinessException('No surat sudah digunakan');
            }

            // Validasi relasi berdasarkan tipe
            if ($post['tipe'] === 'pabrik' && empty($post['pabrik_id'])) {
                throw new BusinessException('Pabrik wajib dipilih untuk tipe Pabrik');
            }
            if ($post['tipe'] === 'retur' && empty($post['toko_id'])) {
                throw new BusinessException('Toko wajib dipilih untuk tipe Retur');
            }

            $barangMasuk = BarangMasuk::create([
                'no_surat' => $post['no_surat'],
                'tanggal' => $post['tanggal'],
                'tipe' => $post['tipe'],
                'pabrik_id' => ! empty($post['pabrik_id']) ? $post['pabrik_id'] : null,
                'toko_id' => ! empty($post['toko_id']) ? $post['toko_id'] : null,
                'keterangan' => $post['keterangan'] ?? null,
            ]);

            $id = $barangMasuk->id;

            foreach ($detailBarang as $barangId => $qty) {
                BarangMasukDetail::create([
                    'barang_masuk_id' => $id,
                    'barang_id' => $barangId,
                    'qty_baik' => $qty['qty_baik'],
                    'qty_rusak' => $qty['qty_rusak'],
                ]);

                $this->stokService->tambahStok(
                    $barangId,
                    $qty['qty_baik'],
                    $qty['qty_rusak']
                );
            }

            DB::commit();

            $this->writeAuditLog('create', $id, 'Barang masuk berhasil disimpan', [
                'no_surat' => $post['no_surat'],
                'tipe' => $post['tipe'],
                'detail' => $detailBarang,
            ]);

            $barangNames = [];
            foreach ($detailBarang as $barangId => $qty) {
                $b = Barang::find($barangId);
                $totalQty = $qty['qty_baik'] + $qty['qty_rusak'];
                $barangNames[] = $totalQty.'x '.($b ? $b->nama_barang : 'Barang');
            }
            $message = $post['no_surat'].': '.implode(', ', $barangNames);

            Notification::notify('Barang Masuk Baru', $message, 'barang_masuk', $id);

            return redirect()->route('transaksi.barangmasuk.index')->with('success', 'Berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $this->getSafeErrorMessage($e))
                ->with('field_errors', $this->mapFieldError($e->getMessage()));
        }
    }

    public function edit($id)
    {
        $this->requireAdmin();

        try {
            $barangMasuk = BarangMasuk::findOrFail($id);
        } catch (\Exception $e) {
            return redirect()->route('transaksi.barangmasuk.index')->with('error', 'Data barang masuk tidak ditemukan');
        }

        $detail = BarangMasukDetail::where('barang_masuk_id', $barangMasuk->id)->get();

        return view('transaksi.barang-masuk.edit', [
            'title' => 'Edit Barang Masuk',
            'data' => $barangMasuk,
            'detail' => $detail,
            'stokPerBarang' => $this->getStokPerBarang($detail),
            'barang' => Barang::orderBy('nama_barang', 'ASC')->get(),
            'pabrik' => Pabrik::orderBy('nama_pabrik', 'ASC')->get(),
            'toko' => Toko::orderBy('nama_toko', 'ASC')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->requireAdmin();
        $post = $request->all();
        $barangMasuk = BarangMasuk::findOrFail($id);

        if (empty($post['barang_id'])) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Detail barang wajib diisi');
        }

        try {
            $detailBarang = $this->buildDetailBarang($post);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $this->getSafeErrorMessage($e));
        }

        DB::beginTransaction();

        try {
            $existing = BarangMasuk::where('no_surat', $post['no_surat'])
                ->where('id', '!=', $barangMasuk->id)
                ->first();

            if ($existing) {
                throw new BusinessException('No surat sudah digunakan');
            }

            $oldDetail = BarangMasukDetail::where('barang_masuk_id', $barangMasuk->id)->get();

            foreach ($oldDetail as $d) {
                // Tidak perlu lockStok() manual — kurangStok() sudah lock otomatis
                $this->stokService->kurangStok(
                    $d->barang_id,
                    $d->qty_baik,
                    $d->qty_rusak
                );
            }

            BarangMasukDetail::where('barang_masuk_id', $barangMasuk->id)->delete();

            $barangMasuk->update([
                'no_surat' => $post['no_surat'],
                'tanggal' => $post['tanggal'],
                'tipe' => $post['tipe'],
                'pabrik_id' => ! empty($post['pabrik_id']) ? $post['pabrik_id'] : null,
                'toko_id' => ! empty($post['toko_id']) ? $post['toko_id'] : null,
                'keterangan' => $post['keterangan'] ?? null,
            ]);

            foreach ($detailBarang as $barangId => $qty) {
                BarangMasukDetail::create([
                    'barang_masuk_id' => $barangMasuk->id,
                    'barang_id' => $barangId,
                    'qty_baik' => $qty['qty_baik'],
                    'qty_rusak' => $qty['qty_rusak'],
                ]);

                $this->stokService->tambahStok(
                    $barangId,
                    $qty['qty_baik'],
                    $qty['qty_rusak']
                );
            }

            DB::commit();

            $this->writeAuditLog('update', $barangMasuk->id, 'Barang masuk berhasil diupdate', [
                'no_surat' => $post['no_surat'],
                'tipe' => $post['tipe'],
                'old_detail' => $oldDetail->toArray(),
                'new_detail' => $detailBarang,
            ]);

            $barangNames = [];
            foreach ($detailBarang as $barangId => $qty) {
                $b = Barang::find($barangId);
                $totalQty = $qty['qty_baik'] + $qty['qty_rusak'];
                $barangNames[] = $totalQty.'x '.($b ? $b->nama_barang : 'Barang');
            }
            $message = $post['no_surat'].': '.implode(', ', $barangNames);

            Notification::notify('Barang Masuk Diupdate', $message, 'barang_masuk', $barangMasuk->id);

            return redirect()->route('transaksi.barangmasuk.index')->with('success', 'Berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();

            $failedDetail = [];
            if (isset($oldDetail)) {
                $failedDetail = $oldDetail->toArray();
            }
            try {
                $this->writeAuditLog(
                    'update_gagal',
                    $barangMasuk->id,
                    'Gagal update barang masuk: '.$this->getSafeErrorMessage($e),
                    [
                        'input' => $post,
                        'old_detail' => $failedDetail,
                        'error' => $this->getSafeErrorMessage($e),
                    ]
                );
            } catch (\Exception $auditErr) {
                \Log::error('Gagal menulis audit log: '.$auditErr->getMessage());
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $this->getSafeErrorMessage($e));
        }
    }

    public function destroy(BarangMasuk $barangMasuk)
    {
        $this->requireAdmin();

        DB::beginTransaction();

        try {
            $detail = BarangMasukDetail::where('barang_masuk_id', $barangMasuk->id)->get();

            foreach ($detail as $d) {
                $this->stokService->kurangStok(
                    $d->barang_id,
                    $d->qty_baik,
                    $d->qty_rusak
                );
            }

            BarangMasukDetail::where('barang_masuk_id', $barangMasuk->id)->delete();
            $barangMasuk->delete();

            DB::commit();

            $this->writeAuditLog('delete', $barangMasuk->id, 'Barang masuk berhasil dihapus', [
                'header' => $barangMasuk->toArray(),
                'detail' => $detail->toArray(),
            ]);

            return redirect()->route('transaksi.barangmasuk.index')->with('success', 'Berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', $this->getSafeErrorMessage($e));
        }
    }

    public function detail(BarangMasuk $barangMasuk)
    {
        $detail = BarangMasukDetail::query()
            ->select('barang_masuk_detail.*', 'barang.kode_barang', 'barang.nama_barang')
            ->join('barang', 'barang.id', '=', 'barang_masuk_detail.barang_id')
            ->where('barang_masuk_id', $barangMasuk->id)
            ->paginate(20);

        return view('transaksi.barang-masuk.detail', [
            'title' => 'Detail Barang Masuk',
            'barangMasuk' => $barangMasuk,
            'detail' => $detail,
        ]);
    }

    protected function getStokPerBarang($detail): array
    {
        $stokPerBarang = [];
        foreach ($detail as $d) {
            $stok = $this->stokService->getByBarang($d->barang_id);
            if ($stok) {
                $stokPerBarang[$d->barang_id] = $stok;
            }
        }

        return $stokPerBarang;
    }

    protected function buildDetailBarang(array $post): array
    {
        $detail = [];
        foreach ((array) ($post['barang_id'] ?? []) as $i => $barangId) {
            $barangId = (int) $barangId;
            if ($barangId <= 0) {
                continue;
            }

            $qtyBaik = (int) ($post['qty_baik'][$i] ?? 0);
            $qtyRusak = (int) ($post['qty_rusak'][$i] ?? 0);

            if ($qtyBaik < 0 || $qtyRusak < 0) {
                throw new BusinessException('Jumlah tidak boleh negatif');
            }

            if ($qtyBaik === 0 && $qtyRusak === 0) {
                continue;
            }

            $detail[$barangId] = [
                'qty_baik' => $qtyBaik,
                'qty_rusak' => $qtyRusak,
            ];
        }

        if (empty($detail)) {
            throw new BusinessException('Detail barang wajib diisi');
        }

        $validIds = Barang::select('id')->whereIn('id', array_keys($detail))->pluck('id')->toArray();
        foreach (array_keys($detail) as $bid) {
            if (! in_array($bid, $validIds)) {
                throw new BusinessException('Barang ID '.$bid.' tidak ditemukan');
            }
        }

        return $detail;
    }
}
