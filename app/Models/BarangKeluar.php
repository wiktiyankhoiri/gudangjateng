<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BarangKeluar extends Model
{
    use HasFactory;

    protected $table = 'barang_keluar';

    protected $fillable = [
        'no_surat',
        'tanggal',
        'toko_id',
        'sales_id',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function toko(): BelongsTo
    {
        return $this->belongsTo(Toko::class);
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(BarangKeluarDetail::class);
    }
}
