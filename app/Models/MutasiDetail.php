<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutasiDetail extends Model
{
    use HasFactory;

    protected $table = 'mutasi_detail';

    protected $fillable = [
        'mutasi_id',
        'barang_id',
        'tipe',
        'qty',
    ];

    public function mutasi(): BelongsTo
    {
        return $this->belongsTo(Mutasi::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
