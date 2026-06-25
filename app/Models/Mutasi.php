<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mutasi extends Model
{
    use HasFactory;

    protected $table = 'mutasi';

    protected $fillable = [
        'no_mutasi',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(MutasiDetail::class);
    }

    /**
     * Generate nomor mutasi otomatis.
     * Format: {prefix}{Y}{m}{nnn} — reset per tahun
     * Contoh: M202606001 (Mutasi, Juni 2026, no 1)
     */
    public static function generateNoMutasi(string $prefix = 'M'): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');

        $last = static::where('no_mutasi', 'LIKE', $prefix . $year . '%')
            ->orderBy('id', 'desc')
            ->first();

        $nextSeq = 1;
        if ($last && preg_match('/' . $prefix . $year . '\d{2}(\d+)$/', $last->no_mutasi, $matches)) {
            $nextSeq = (int) $matches[1] + 1;
        }

        return $prefix . $year . $month . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
    }
}
