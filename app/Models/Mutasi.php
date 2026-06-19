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
     * Format: DAT{ddMMyyyy}-{nnn} (urutan global, tidak pernah reset)
     */
    public static function generateNoMutasi(): string
    {
        $today = now()->format('dmY');

        // Ambil nomor urut tertinggi dari mutasi terakhir
        $last = static::orderBy('id', 'desc')->first();

        $nextSeq = 1;
        if ($last && preg_match('/-(\d+)$/', $last->no_mutasi, $matches)) {
            $nextSeq = (int) $matches[1] + 1;
        }

        return 'DAT' . $today . '-' . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
    }
}
