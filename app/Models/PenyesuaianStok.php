<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenyesuaianStok extends Model
{
    use HasFactory;

    protected $table = 'penyesuaian_stok';

    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'barang_id',
        'stok_baik_sebelum',
        'stok_baik_sesudah',
        'stok_rusak_sebelum',
        'stok_rusak_sesudah',
        'stok_sales_sebelum',
        'stok_sales_sesudah',
        'selisih_baik',
        'selisih_rusak',
        'selisih_sales',
        'alasan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
