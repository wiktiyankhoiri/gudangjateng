<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BarangMasuk extends Model
{
    use HasFactory;

    protected $table = 'barang_masuk';

    protected $fillable = [
        'no_surat',
        'tanggal',
        'tipe',
        'pabrik_id',
        'toko_id',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pabrik(): BelongsTo
    {
        return $this->belongsTo(Pabrik::class);
    }

    public function toko(): BelongsTo
    {
        return $this->belongsTo(Toko::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(BarangMasukDetail::class);
    }
}
