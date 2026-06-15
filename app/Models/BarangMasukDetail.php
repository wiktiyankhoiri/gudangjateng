<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangMasukDetail extends Model
{
    use HasFactory;

    protected $table = 'barang_masuk_detail';

    public $timestamps = false;

    protected $fillable = [
        'barang_masuk_id',
        'barang_id',
        'qty_baik',
        'qty_rusak',
    ];

    public function barangMasuk(): BelongsTo
    {
        return $this->belongsTo(BarangMasuk::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
