<?php

namespace App\Http\Controllers;

use App\Models\Mutasi;
use App\Models\MutasiDetail;
use App\Models\Barang;
use App\Models\Stok;
use App\Services\StokService;
use App\Models\Notification;
use App\Exceptions\BusinessException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MutasiController extends Controller
{
    public function __construct(
        protected StokService $stokService
    ) {}

    public function index()
    {
        $this->requireAdmin();

        $data = Mutasi::with('details')->orderBy('id', 'DESC')->paginate(50);

        foreach ($data as $m) {
            $grouped = [];
            $totalQty = 0;
            foreach ($m->details as $d) {
                $grouped[$d->tipe] = ($grouped[$d->tipe] ?? 0) + (int)$d->qty;
                $totalQty += (int)$d->qty;
            }
            $summary = [];
            foreach ($grouped as $tipe => $qty) {
                $summary[] = $tipe . ':' . $qty;
            }
            $m->detail_summary = implode(', ', $summary);
            $m->total_qty = $totalQty;
        }

        return view('transaksi.mutasi.index', [
            'title' => 'Mutasi',
            'mutasi' => $data,
        ]);
    }

    public function create()
    {
        $this->requireAdmin();

        $stokPerBarang = [];
        $allStok = Stok::all();
        foreach ($allStok as $s) {
            $stokPerBarang[$s->barang_id] = [
                'stok_baik' => (int) $s->stok_baik,
                'stok_rusak' => (int) $s->stok_rusak,
            ];
        }

        return view('transaksi.mutasi.create', [
            'title' => 'Tambah Mutasi',
            'barang' => Barang::orderBy('nama_barang', 'ASC')->get(),
            'stokPerBarang' => $stokPerBarang,
            'noMutasi' => Mutasi::generateNoMutasi('M'),
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
                ->with('error', 'Detail mutasi wajib diisi');
        }

        if (empty($post['tipe']) || !in_array($post['tipe'], ['baik_ke_rusak', 'rusak_ke_baik'], true)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Tipe mutasi tidak valid');
        }

        try {
            $group = $this->buildGroup($post, $post['tipe']);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $this->getSafeErrorMessage($e))
                ->with('field_errors', $this->mapFieldError($e->getMessage()));
        }

        DB::beginTransaction();

        try {
            $noMutasi = Mutasi::generateNoMutasi('M');

            $mutasi = Mutasi::create([
                'no_mutasi' => $noMutasi,
                'tanggal' => $post['tanggal'],
                'keterangan' => $post['keterangan'],
            ]);

            $mutasiId = $mutasi->id;

            foreach ($group as $g) {
                $stok = $this->stokService->lockStok($g['barang_id']);

                if (!$stok) {
                    throw new BusinessException('Stok barang tidak ditemukan');
                }

                if (
                    $g['tipe'] === 'baik_ke_rusak'
                    &&
                    $g['qty'] > (int)$stok['stok_baik']
                ) {
                    $barang = Barang::find($g['barang_id']);
                    throw new BusinessException(
                        'Jumlah yang Anda masukkan melebihi stok baik: '
                        . ($barang ? $barang->nama_barang : 'Barang')
                    );
                }

                if (
                    $g['tipe'] === 'rusak_ke_baik'
                    &&
                    $g['qty'] > (int)$stok['stok_rusak']
                ) {
                    $barang = Barang::find($g['barang_id']);
                    throw new BusinessException(
                        'Jumlah yang Anda masukkan melebihi stok rusak: '
                        . ($barang ? $barang->nama_barang : 'Barang')
                    );
                }

                MutasiDetail::create([
                    'mutasi_id' => $mutasiId,
                    'barang_id' => $g['barang_id'],
                    'tipe' => $g['tipe'],
                    'qty' => $g['qty'],
                ]);

                $this->stokService->mutasiStok(
                    $g['barang_id'],
                    $g['tipe'],
                    $g['qty']
                );
            }

            DB::commit();

            $this->writeAuditLog('create', $mutasiId, 'Mutasi berhasil disimpan', [
                'no_mutasi' => $noMutasi,
                'detail' => $group,
            ]);

            $barangNames = [];
            foreach ($group as $g) {
                $b = Barang::find($g['barang_id']);
                $tipeLabel = $g['tipe'] === 'baik_ke_rusak' ? 'Baik→Rusak' : 'Rusak→Baik';
                $barangNames[] = ($b ? $b->nama_barang : 'Barang') . ' (' . $tipeLabel . ' ' . $g['qty'] . ')';
            }
            $message = $noMutasi . ': ' . implode(', ', $barangNames);

            // Notifikasi mutasi dinonaktifkan - hanya BM & BK
            // Notification::notify('Mutasi Baru', $message, 'mutasi', $mutasiId);

            return redirect()->route('transaksi.mutasi.index')->with('success', 'Mutasi berhasil disimpan');

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
            $mutasi = Mutasi::findOrFail($id);
        } catch (\Exception $e) {
            return redirect()->route('transaksi.mutasi.index')->with('error', 'Data mutasi tidak ditemukan');
        }

        $detail = MutasiDetail::where('mutasi_id', $mutasi->id)->get();

        return view('transaksi.mutasi.edit', [
            'title' => 'Edit Mutasi',
            'mutasi' => $mutasi,
            'detail' => $detail,
            'stokPerBarang' => $this->getStokPerBarang($detail),
            'barang' => Barang::orderBy('nama_barang', 'ASC')->get(),
        ]);
    }

    public function update(Request $request, Mutasi $mutasi)
    {
        $this->requireAdmin();
        $post = $request->all();

        if (empty($post['barang_id'])) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Detail mutasi wajib diisi');
        }

        if (empty($post['tipe']) || !in_array($post['tipe'], ['baik_ke_rusak', 'rusak_ke_baik'], true)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Tipe mutasi tidak valid');
        }

        try {
            $group = $this->buildGroup($post, $post['tipe']);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $this->getSafeErrorMessage($e));
        }

        DB::beginTransaction();

        try {
            $oldDetail = MutasiDetail::where('mutasi_id', $mutasi->id)->get();

            foreach ($oldDetail as $d) {
                $this->stokService->rollbackStok(
                    $d->barang_id,
                    $d->tipe,
                    (int)$d->qty
                );
            }

            MutasiDetail::where('mutasi_id', $mutasi->id)->delete();

            $mutasi->update([
                'tanggal' => $post['tanggal'],
                'keterangan' => $post['keterangan'],
            ]);

            foreach ($group as $g) {
                $stok = $this->stokService->lockStok($g['barang_id']);

                if (!$stok) {
                    throw new BusinessException('Stok barang tidak ditemukan');
                }

                if (
                    $g['tipe'] === 'baik_ke_rusak'
                    &&
                    $g['qty'] > (int)$stok['stok_baik']
                ) {
                    $barang = Barang::find($g['barang_id']);
                    throw new BusinessException(
                        'Jumlah yang Anda masukkan melebihi stok baik: '
                        . ($barang ? $barang->nama_barang : 'Barang')
                    );
                }

                if (
                    $g['tipe'] === 'rusak_ke_baik'
                    &&
                    $g['qty'] > (int)$stok['stok_rusak']
                ) {
                    $barang = Barang::find($g['barang_id']);
                    throw new BusinessException(
                        'Jumlah yang Anda masukkan melebihi stok rusak: '
                        . ($barang ? $barang->nama_barang : 'Barang')
                    );
                }
            }

            foreach ($group as $g) {
                MutasiDetail::create([
                    'mutasi_id' => $mutasi->id,
                    'barang_id' => $g['barang_id'],
                    'tipe' => $g['tipe'],
                    'qty' => $g['qty'],
                ]);

                $this->stokService->mutasiStok(
                    $g['barang_id'],
                    $g['tipe'],
                    $g['qty']
                );
            }

            DB::commit();

            $this->writeAuditLog('update', $mutasi->id, 'Mutasi berhasil diupdate', [
                'no_mutasi' => $mutasi->no_mutasi,
                'old_detail' => $oldDetail->toArray(),
                'new_detail' => $group,
            ]);

            $barangNames = [];
            foreach ($group as $g) {
                $b = Barang::find($g['barang_id']);
                $tipeLabel = $g['tipe'] === 'baik_ke_rusak' ? 'Baik→Rusak' : 'Rusak→Baik';
                $barangNames[] = ($b ? $b->nama_barang : 'Barang') . ' (' . $tipeLabel . ' ' . $g['qty'] . ')';
            }
            $message = $mutasi->no_mutasi . ': ' . implode(', ', $barangNames);

            // Notifikasi mutasi dinonaktifkan
            // Notification::notify('Mutasi Diupdate', $message, 'mutasi', $mutasi->id);

            return redirect()->route('transaksi.mutasi.index')->with('success', 'Mutasi berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();

            $failedDetail = [];
            if (isset($oldDetail)) {
                $failedDetail = $oldDetail->toArray();
            }
            try {
                $this->writeAuditLog(
                    'update_gagal',
                    $mutasi->id,
                    'Gagal update mutasi: ' . $this->getSafeErrorMessage($e),
                    [
                        'input' => $post,
                        'old_detail' => $failedDetail,
                        'error' => $this->getSafeErrorMessage($e),
                    ]
                );
            } catch (\Exception $auditErr) {
                \Log::error('Gagal menulis audit log: ' . $auditErr->getMessage());
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $this->getSafeErrorMessage($e));
        }
    }

    public function destroy(Mutasi $mutasi)
    {
        $this->requireAdmin();

        DB::beginTransaction();

        try {
            $detail = MutasiDetail::where('mutasi_id', $mutasi->id)->get();

            foreach ($detail as $d) {
                $this->stokService->rollbackStok(
                    $d->barang_id,
                    $d->tipe,
                    (int)$d->qty
                );
            }

            MutasiDetail::where('mutasi_id', $mutasi->id)->delete();
            $mutasi->delete();

            DB::commit();

            $this->writeAuditLog('delete', $mutasi->id, 'Mutasi berhasil dihapus', [
                'header' => $mutasi->toArray(),
                'detail' => $detail->toArray(),
            ]);

            return redirect()->route('transaksi.mutasi.index')->with('success', 'Mutasi berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', $this->getSafeErrorMessage($e));
        }
    }

    public function detail(Mutasi $mutasi)
    {
        $detail = MutasiDetail::query()
            ->select('mutasi_detail.*', 'barang.kode_barang', 'barang.nama_barang')
            ->join('barang', 'barang.id', '=', 'mutasi_detail.barang_id')
            ->where('mutasi_id', $mutasi->id)
            ->paginate(20);

        return view('transaksi.mutasi.detail', [
            'title' => 'Detail Mutasi',
            'mutasi' => $mutasi,
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

    protected function buildGroup(array $post, string $tipe): array
    {
        $group = [];
        $seenBarang = [];
        foreach ((array) ($post['barang_id'] ?? []) as $i => $barangId) {
            $barangId = (int) $barangId;
            if ($barangId <= 0) continue;

            $qty = (int) ($post['qty'][$i] ?? 0);

            if ($qty <= 0) throw new BusinessException('Jumlah harus lebih dari 0');

            if (isset($seenBarang[$barangId])) {
                throw new BusinessException('Barang duplikat tidak diperbolehkan dalam satu mutasi');
            }
            $seenBarang[$barangId] = true;

            $group[] = ['barang_id' => $barangId, 'tipe' => $tipe, 'qty' => $qty];
        }

        if (empty($group)) throw new BusinessException('Detail mutasi wajib diisi');

        $validIds = Barang::select('id')->whereIn('id', array_column($group, 'barang_id'))->pluck('id')->toArray();
        foreach ($group as $g) {
            if (!in_array($g['barang_id'], $validIds)) {
                throw new \Exception('Barang ID ' . $g['barang_id'] . ' tidak ditemukan');
            }
        }

        return $group;
    }
}
