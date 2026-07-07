<?php

namespace App\Http\Controllers;

use App\Exceptions\BusinessException;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarDetail;
use App\Models\Notification;
use App\Models\Toko;
use App\Models\User;
use App\Services\StokService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangKeluarController extends Controller
{
    public function __construct(
        protected StokService $stokService
    ) {}

    public function index(Request $request)
    {
        $this->requireAdmin();

        $cari = $request->get('cari');

        $query = BarangKeluar::query()
            ->select('barang_keluar.*', 'toko.nama_toko', 'sales.nama as nama_sales')
            ->selectRaw("CASE WHEN barang_keluar.sumber = 'sales' THEN 'Sales' ELSE 'Gudang' END as sumber")
            ->selectSub(function ($q) {
                $q->from('barang_keluar_detail')
                    ->whereColumn('barang_keluar_id', 'barang_keluar.id')
                    ->selectRaw('COUNT(*)');
            }, 'total_item')
            ->leftJoin('toko', 'toko.id', '=', 'barang_keluar.toko_id')
            ->leftJoin('users as sales', 'sales.id', '=', 'barang_keluar.sales_id');

        if ($cari) {
            $query->where(function ($w) use ($cari) {
                $w->where('barang_keluar.no_surat', 'ILIKE', "%{$cari}%")
                    ->orWhere('barang_keluar.keterangan', 'ILIKE', "%{$cari}%")
                    ->orWhere('toko.nama_toko', 'ILIKE', "%{$cari}%")
                    ->orWhere('sales.nama', 'ILIKE', "%{$cari}%");
            });
        }

        $data = $query->orderBy('barang_keluar.tanggal', 'DESC')->orderBy('barang_keluar.id', 'DESC')->paginate(50);

        return view('transaksi.barang-keluar.index', [
            'title' => 'Barang Keluar',
            'data' => $data,
            'cari' => $cari,
        ]);
    }

    public function create()
    {
        $this->requireAdmin();

        return view('transaksi.barang-keluar.create', [
            'title' => 'Tambah Barang Keluar',
            'barang' => Barang::orderBy('nama_barang', 'ASC')->get(),
            'toko' => Toko::orderBy('nama_toko', 'ASC')->get(),
            'salesList' => User::where('role', 'sales')->orderBy('nama', 'ASC')->get(),
            'stokAll' => $this->stokService->getAllStok(),
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
            $totalQty = $this->buildTotalQty($post);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $this->getSafeErrorMessage($e));
        }

        DB::beginTransaction();

        try {
            $existing = BarangKeluar::where('no_surat', $post['no_surat'])->first();

            if ($existing) {
                throw new BusinessException('No surat sudah digunakan');
            }

            $barangKeluar = BarangKeluar::create([
                'no_surat' => $post['no_surat'],
                'tanggal' => $post['tanggal'],
                'toko_id' => ! empty($post['toko_id']) ? $post['toko_id'] : null,
                'sales_id' => ! empty($post['sales_id']) ? $post['sales_id'] : null,
                'sumber' => $post['sumber'] ?? 'gudang',
                'keterangan' => $post['keterangan'] ?? null,
            ]);

            $id = $barangKeluar->id;
            $sumber = $barangKeluar->sumber;

            foreach ($totalQty as $barangId => $qty) {
                $stok = $this->stokService->lockStok($barangId);
                if (! $stok) {
                    throw new BusinessException('Stok barang tidak ditemukan');
                }
                if ($sumber === 'sales') {
                    if ((int) $stok['stok_sales'] < $qty) {
                        $barang = Barang::find($barangId);
                        throw new BusinessException(
                            'Stok sales tidak mencukupi untuk barang: '
                            .($barang ? $barang->nama_barang : 'Barang')
                        );
                    }
                } else {
                    if ((int) $stok['stok_baik'] < $qty) {
                        $barang = Barang::find($barangId);
                        throw new BusinessException(
                            'Stok baik tidak mencukupi untuk barang: '
                            .($barang ? $barang->nama_barang : 'Barang')
                        );
                    }
                }
            }

            foreach ($totalQty as $barangId => $qty) {
                BarangKeluarDetail::create([
                    'barang_keluar_id' => $id,
                    'barang_id' => $barangId,
                    'qty_baik' => $qty,
                    'qty_rusak' => 0,
                ]);

                if ($sumber === 'sales') {
                    $this->stokService->kurangStokSales($barangId, $qty);
                } else {
                    $this->stokService->kurangStok($barangId, $qty, 0);
                }
            }

            DB::commit();

            $this->writeAuditLog('create', $id, 'Barang keluar berhasil disimpan', [
                'no_surat' => $post['no_surat'],
                'detail' => $totalQty,
            ]);

            $message = $post['no_surat'].' · '.count($totalQty).' item ('.array_sum($totalQty).' pcs)';

            Notification::notify('Barang Keluar Baru', $message, 'barang_keluar', $id);

            return redirect()->route('transaksi.barangkeluar.index')->with('success', 'Berhasil disimpan');

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
            $barangKeluar = BarangKeluar::findOrFail($id);
        } catch (\Exception $e) {
            return redirect()->route('transaksi.barangkeluar.index')->with('error', 'Data barang keluar tidak ditemukan');
        }

        $detail = BarangKeluarDetail::where('barang_keluar_id', $barangKeluar->id)->get();

        return view('transaksi.barang-keluar.edit', [
            'title' => 'Edit Barang Keluar',
            'data' => $barangKeluar,
            'detail' => $detail,
            'stokPerBarang' => $this->getStokPerBarang($detail),
            'allStok' => $this->stokService->getAllStok(),
            'barang' => Barang::orderBy('nama_barang', 'ASC')->get(),
            'toko' => Toko::orderBy('nama_toko', 'ASC')->get(),
            'salesList' => User::where('role', 'sales')->orderBy('nama', 'ASC')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->requireAdmin();
        $post = $request->all();
        $barangKeluar = BarangKeluar::findOrFail($id);

        if (empty($post['barang_id'])) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Detail barang wajib diisi');
        }

        try {
            $totalQty = $this->buildTotalQty($post);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $this->getSafeErrorMessage($e));
        }

        DB::beginTransaction();

        try {
            $existing = BarangKeluar::where('no_surat', $post['no_surat'])
                ->where('id', '!=', $barangKeluar->id)
                ->first();

            if ($existing) {
                throw new BusinessException('No surat sudah digunakan');
            }

            $oldDetail = BarangKeluarDetail::where('barang_keluar_id', $barangKeluar->id)->get();
            $sumberLama = $barangKeluar->sumber;
            $sumberBaru = $post['sumber'] ?? 'gudang';

            foreach ($oldDetail as $d) {
                if ($sumberLama === 'sales') {
                    $this->stokService->tambahStokSales($d->barang_id, $d->qty_baik);
                } else {
                    $this->stokService->tambahStok($d->barang_id, $d->qty_baik, 0);
                }
            }

            foreach ($totalQty as $barangId => $qty) {
                $stok = $this->stokService->lockStok($barangId);

                if (! $stok) {
                    throw new BusinessException('Stok barang tidak ditemukan');
                }

                if ($sumberBaru === 'sales') {
                    if ((int) $stok['stok_sales'] < $qty) {
                        $barang = Barang::find($barangId);
                        throw new BusinessException(
                            'Stok sales tidak mencukupi untuk barang: '
                            .($barang ? $barang->nama_barang : 'Barang')
                        );
                    }
                } else {
                    if ((int) $stok['stok_baik'] < $qty) {
                        $barang = Barang::find($barangId);
                        throw new BusinessException(
                            'Stok baik tidak mencukupi untuk barang: '
                            .($barang ? $barang->nama_barang : 'Barang')
                        );
                    }
                }
            }

            BarangKeluarDetail::where('barang_keluar_id', $barangKeluar->id)->delete();

            $barangKeluar->update([
                'no_surat' => $post['no_surat'],
                'tanggal' => $post['tanggal'],
                'toko_id' => ! empty($post['toko_id']) ? $post['toko_id'] : null,
                'sales_id' => ! empty($post['sales_id']) ? $post['sales_id'] : null,
                'sumber' => $sumberBaru,
                'keterangan' => $post['keterangan'] ?? null,
            ]);

            foreach ($totalQty as $barangId => $qty) {
                BarangKeluarDetail::create([
                    'barang_keluar_id' => $barangKeluar->id,
                    'barang_id' => $barangId,
                    'qty_baik' => $qty,
                    'qty_rusak' => 0,
                ]);

                if ($sumberBaru === 'sales') {
                    $this->stokService->kurangStokSales($barangId, $qty);
                } else {
                    $this->stokService->kurangStok($barangId, $qty, 0);
                }
            }

            DB::commit();

            $this->writeAuditLog('update', $barangKeluar->id, 'Barang keluar berhasil diupdate', [
                'no_surat' => $post['no_surat'],
                'old_detail' => $oldDetail->toArray(),
                'new_detail' => $totalQty,
            ]);

            $message = $post['no_surat'].' · '.count($totalQty).' item ('.array_sum($totalQty).' pcs)';

            Notification::notify('Barang Keluar Diupdate', $message, 'barang_keluar', $barangKeluar->id);

            return redirect()->route('transaksi.barangkeluar.index')->with('success', 'Berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();

            $failedDetail = [];
            if (isset($oldDetail)) {
                $failedDetail = $oldDetail->toArray();
            }
            try {
                $this->writeAuditLog(
                    'update_gagal',
                    $barangKeluar->id,
                    'Gagal update barang keluar: '.$this->getSafeErrorMessage($e),
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

    public function destroy(BarangKeluar $barangKeluar)
    {
        $this->requireAdmin();

        DB::beginTransaction();

        try {
            $detail = BarangKeluarDetail::where('barang_keluar_id', $barangKeluar->id)->get();

            foreach ($detail as $d) {
                if ($barangKeluar->sumber === 'sales') {
                    $this->stokService->tambahStokSales($d->barang_id, $d->qty_baik);
                } else {
                    $this->stokService->tambahStok($d->barang_id, $d->qty_baik, 0);
                }
            }

            BarangKeluarDetail::where('barang_keluar_id', $barangKeluar->id)->delete();
            $barangKeluar->delete();

            DB::commit();

            $this->writeAuditLog('delete', $barangKeluar->id, 'Barang keluar berhasil dihapus', [
                'header' => $barangKeluar->toArray(),
                'detail' => $detail->toArray(),
            ]);

            return redirect()->route('transaksi.barangkeluar.index')->with('success', 'Berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', $this->getSafeErrorMessage($e));
        }
    }

    public function detail(BarangKeluar $barangKeluar)
    {
        $header = BarangKeluar::query()
            ->select('barang_keluar.*', 'toko.nama_toko', 'sales.nama as nama_sales')
            ->leftJoin('toko', 'toko.id', '=', 'barang_keluar.toko_id')
            ->leftJoin('users as sales', 'sales.id', '=', 'barang_keluar.sales_id')
            ->where('barang_keluar.id', $barangKeluar->id)
            ->first();

        if (! $header) {
            abort(404, 'Data tidak ditemukan');
        }

        $detail = BarangKeluarDetail::query()
            ->select('barang_keluar_detail.*', 'barang.kode_barang', 'barang.nama_barang')
            ->join('barang', 'barang.id', '=', 'barang_keluar_detail.barang_id')
            ->where('barang_keluar_id', $barangKeluar->id)
            ->paginate(20);

        return view('transaksi.barang-keluar.detail', [
            'title' => 'Detail Barang Keluar',
            'barangKeluar' => $header,
            'detail' => $detail,
        ]);
    }

    protected function buildTotalQty(array $post): array
    {
        $detail = [];
        foreach ((array) ($post['barang_id'] ?? []) as $i => $barangId) {
            $barangId = (int) $barangId;
            if ($barangId <= 0) {
                continue;
            }

            $qtyBaik = (int) ($post['qty_baik'][$i] ?? 0);

            if ($qtyBaik < 0) {
                throw new BusinessException('Jumlah tidak boleh negatif');
            }

            $detail[$barangId] = $qtyBaik;
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
}
