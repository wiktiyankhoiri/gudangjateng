<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InitialStok extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'initialstok';

    protected $fillable = [
        'barang_id',
        'qty_baik',
        'qty_rusak',
        'keterangan',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
