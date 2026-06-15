<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected array $fieldErrors = [];

    protected function getSafeErrorMessage(\Throwable $e, string $fallback = 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.'): string
    {
        $message = $e->getMessage();
        \Log::error('Error: ' . $message . ' | Trace: ' . $e->getTraceAsString());

        if ($e instanceof \App\Exceptions\BusinessException) {
            return $message;
        }

        if (str_contains(strtolower($message), 'foreign key') || str_contains(strtolower($message), 'violates foreign key')) {
            return 'Data tidak dapat dihapus atau diubah karena masih digunakan di transaksi/tabel lain.';
        }

        return $fallback;
    }

    protected function requireSuperAdmin(): void
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Akses ditolak — hanya Super Admin');
        }
    }

    protected function requireAdmin(): void
    {
        $role = auth()->user()->role;
        if ($role !== 'admin' && $role !== 'super_admin') {
            abort(403, 'Akses ditolak');
        }
    }

    protected function requireAudit(): void
    {
        $role = auth()->user()->role;
        if ($role !== 'audit' && $role !== 'super_admin') {
            abort(403, 'Akses ditolak — hanya untuk role Audit');
        }
    }

    protected function writeAuditLog(string $action, $referenceId, string $description, array $data = []): void
    {
        try {
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'table_name' => $this->getAuditTable(),
                'reference_id' => $referenceId,
                'description' => $description,
                'data' => json_encode($data),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            \Log::error("Gagal menulis audit log: " . $e->getMessage());
        }
    }

    protected function getAuditTable(): string
    {
        $class = (new \ReflectionClass($this))->getShortName();
        $name = str_replace('Controller', '', $class);
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));
    }

    protected function mapFieldError(string $message): array
    {
        $map = [
            'No surat sudah digunakan' => ['no_surat' => $message],
            'No mutasi sudah digunakan' => ['no_mutasi' => $message],
            'Detail barang wajib diisi' => ['barang_id' => $message],
            'Detail mutasi wajib diisi' => ['barang_id' => $message],
            'Jumlah harus lebih dari 0' => ['qty' => $message],
            'Tipe mutasi tidak valid' => ['tipe' => $message],
            'Stok baik tidak mencukupi' => ['qty' => $message],
            'Stok rusak tidak mencukupi' => ['qty' => $message],
            'Stok barang tidak ditemukan' => ['barang_id' => 'Barang tidak valid'],
        ];
        foreach ($map as $key => $error) {
            if (str_contains($message, $key)) return $error;
        }
        \Log::error('Unmapped field error: ' . $message);
        return [];
    }
}
