<?php

namespace App\Services;

use App\Models\Stok;
use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\DB;

class StokService
{
    public function lockStok(int $barangId): ?array
    {
        $result = DB::select(
            'SELECT * FROM stok WHERE barang_id = ? FOR UPDATE',
            [$barangId]
        );

        if (empty($result)) {
            return null;
        }

        return (array) $result[0];
    }

    public function getByBarang(int $barangId): ?array
    {
        $stok = Stok::where('barang_id', $barangId)->first();
        return $stok ? $stok->toArray() : null;
    }

    public function ensureByBarang(int $barangId): array
    {
        $stok = $this->lockStok($barangId);

        if ($stok) {
            return $stok;
        }

        // Atomic INSERT with ON CONFLICT DO NOTHING (PostgreSQL)
        DB::statement(
            'INSERT INTO stok (barang_id, stok_baik, stok_rusak, updated_at) VALUES (?, 0, 0, NOW()) ON CONFLICT (barang_id) DO NOTHING',
            [$barangId]
        );

        $stok = $this->lockStok($barangId);

        if (!$stok) {
            throw new BusinessException('Gagal membuat stok barang');
        }

        return $stok;
    }

    public function tambahStok(int $barangId, int $baik = 0, int $rusak = 0): bool
    {
        $baik = (int) $baik;
        $rusak = (int) $rusak;

        if ($baik < 0 || $rusak < 0) {
            throw new BusinessException('Jumlah tidak boleh negatif');
        }

        if ($baik === 0 && $rusak === 0) {
            return true;
        }

        $stok = $this->ensureByBarang($barangId);

        DB::statement(
            'UPDATE stok SET stok_baik = stok_baik + ?, stok_rusak = stok_rusak + ?, updated_at = NOW() WHERE id = ?',
            [$baik, $rusak, (int) $stok['id']]
        );

        return true;
    }

    public function kurangStok(int $barangId, int $baik = 0, int $rusak = 0): bool
    {
        $baik = (int) $baik;
        $rusak = (int) $rusak;

        if ($baik < 0 || $rusak < 0) {
            throw new BusinessException('Jumlah tidak boleh negatif');
        }

        if ($baik === 0 && $rusak === 0) {
            return true;
        }

        $stok = $this->lockStok($barangId);

        if (!$stok) {
            throw new BusinessException('Stok barang tidak ditemukan');
        }

        // Validasi hanya untuk qty yang > 0 agar pesan error akurat
        if ($baik > 0 && (int) $stok['stok_baik'] < $baik) {
            throw new BusinessException('Stok baik tidak mencukupi');
        }
        if ($rusak > 0 && (int) $stok['stok_rusak'] < $rusak) {
            throw new BusinessException('Stok rusak tidak mencukupi');
        }

        DB::statement(
            'UPDATE stok SET stok_baik = stok_baik - ?, stok_rusak = stok_rusak - ?, updated_at = NOW() WHERE id = ?',
            [$baik, $rusak, (int) $stok['id']]
        );

        return true;
    }

    public function mutasiStok(int $barangId, string $tipe, int $qty): void
    {
        $this->validateMutasi($tipe, $qty);

        $stok = $this->lockStok($barangId);
        if (!$stok) {
            throw new BusinessException('Stok barang tidak ditemukan');
        }

        // Single atomic query: tidak bisa gagal di tengah
        if ($tipe === 'baik_ke_rusak') {
            DB::statement(
                'UPDATE stok SET stok_baik = stok_baik - ?, stok_rusak = stok_rusak + ?, updated_at = NOW() WHERE id = ?',
                [$qty, $qty, (int) $stok['id']]
            );
        } else {
            DB::statement(
                'UPDATE stok SET stok_baik = stok_baik + ?, stok_rusak = stok_rusak - ?, updated_at = NOW() WHERE id = ?',
                [$qty, $qty, (int) $stok['id']]
            );
        }
    }

    public function rollbackStok(int $barangId, string $tipe, int $qty): void
    {
        $this->validateMutasi($tipe, $qty);

        $stok = $this->lockStok($barangId);
        if (!$stok) {
            throw new BusinessException('Stok barang tidak ditemukan');
        }

        if ($tipe === 'baik_ke_rusak') {
            // Mutasi asli: baik -qty, rusak +qty
            // Rollback: baik +qty, rusak -qty
            $stokBaik = (int) $stok['stok_baik'] + $qty;
            $stokRusak = (int) $stok['stok_rusak'] - $qty;
        } else {
            // Mutasi asli: rusak -qty, baik +qty
            // Rollback: rusak +qty, baik -qty
            $stokBaik = (int) $stok['stok_baik'] - $qty;
            $stokRusak = (int) $stok['stok_rusak'] + $qty;
        }

        // Validasi: jika hasil rollback negatif, berarti transaksi lain
        // sudah mengubah stok secara signifikan sehingga rollback tidak aman.
        if ($stokBaik < 0 || $stokRusak < 0) {
            if ($stokBaik < 0) {
                throw new BusinessException('Stok baik tidak mencukupi');
            }
            throw new BusinessException('Stok rusak tidak mencukupi');
        }

        DB::statement(
            'UPDATE stok SET stok_baik = ?, stok_rusak = ?, updated_at = NOW() WHERE id = ?',
            [$stokBaik, $stokRusak, (int) $stok['id']]
        );
    }

    public function adjustStok(int $barangId, int $stokBaikBaru, int $stokRusakBaru): void
    {
        if ($stokBaikBaru < 0 || $stokRusakBaru < 0) {
            throw new BusinessException('Stok tidak boleh minus');
        }

        $stok = $this->lockStok($barangId);

        if (!$stok) {
            throw new BusinessException('Stok barang tidak ditemukan');
        }

        DB::statement(
            'UPDATE stok SET stok_baik = ?, stok_rusak = ?, updated_at = NOW() WHERE id = ?',
            [$stokBaikBaru, $stokRusakBaru, (int) $stok['id']]
        );
    }

    public function setInitialStok(int $barangId, int $stokBaik, int $stokRusak): void
    {
        if ($stokBaik < 0 || $stokRusak < 0) {
            throw new BusinessException('Jumlah tidak boleh negatif');
        }

        $this->tambahStok($barangId, $stokBaik, $stokRusak);
    }

    public function validasiStok(int $barangId): array
    {
        $stok = $this->lockStok($barangId);

        if (!$stok) {
            throw new BusinessException('Stok barang tidak ditemukan');
        }

        if ((int) $stok['stok_baik'] < 0 || (int) $stok['stok_rusak'] < 0) {
            throw new BusinessException('Stok tidak boleh minus');
        }

        return $stok;
    }

    public function updateStokById(int $id, array $data): bool
    {
        // Validasi: tidak boleh set nilai negatif
        if (isset($data['stok_baik']) && (int) $data['stok_baik'] < 0) {
            throw new BusinessException('Stok baik tidak boleh minus');
        }
        if (isset($data['stok_rusak']) && (int) $data['stok_rusak'] < 0) {
            throw new BusinessException('Stok rusak tidak boleh minus');
        }

        $fields = [];
        $params = [];

        foreach (['stok_baik', 'stok_rusak', 'updated_at'] as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "$col = ?";
                $params[] = $data[$col];
            }
        }

        if (empty($fields)) {
            return true;
        }

        // Lock row sebelum update untuk mencegah race condition
        $stok = DB::select('SELECT id FROM stok WHERE id = ? FOR UPDATE', [$id]);
        if (empty($stok)) {
            throw new BusinessException('Stok barang tidak ditemukan');
        }

        $params[] = (int) $id;
        DB::statement(
            'UPDATE stok SET ' . implode(', ', $fields) . ' WHERE id = ?',
            $params
        );

        return true;
    }

    public function getStokById(int $id): ?array
    {
        $stok = Stok::find($id);
        return $stok ? $stok->toArray() : null;
    }

    public function getAllStok(): array
    {
        return Stok::all()->map(fn($s) => $s->toArray())->toArray();
    }

    private function validateMutasi(string $tipe, int $qty): void
    {
        if (!in_array($tipe, ['baik_ke_rusak', 'rusak_ke_baik'], true)) {
            throw new BusinessException('Tipe mutasi tidak valid');
        }

        if ($qty <= 0) {
            throw new BusinessException('Jumlah mutasi harus lebih dari 0');
        }
    }
}
