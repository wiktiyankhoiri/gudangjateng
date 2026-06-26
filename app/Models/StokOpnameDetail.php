<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokOpnameDetail extends Model
{
    use HasFactory;

    protected $table = 'stok_opname_detail';

    public $timestamps = false;

    protected $fillable = [
        'stok_opname_id',
        'barang_id',
        'stok_sistem_baik',
        'stok_sistem_rusak',
        'stok_sistem_sales',
        'stok_fisik_baik',
        'stok_fisik_rusak',
        'stok_fisik_sales',
        'selisih_baik',
        'selisih_rusak',
        'selisih_sales',
        'keterangan',
    ];

    public function stokOpname(): BelongsTo
    {
        return $this->belongsTo(StokOpname::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
