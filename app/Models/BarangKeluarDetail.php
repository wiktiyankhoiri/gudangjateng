<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangKeluarDetail extends Model
{
    use HasFactory;

    protected $table = 'barang_keluar_detail';

    public $timestamps = false;

    protected $fillable = [
        'barang_keluar_id',
        'barang_id',
        'qty_baik',
        'qty_rusak',
    ];

    public function barangKeluar(): BelongsTo
    {
        return $this->belongsTo(BarangKeluar::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
