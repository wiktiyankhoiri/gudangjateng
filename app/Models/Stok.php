<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stok extends Model
{
    use HasFactory;

    protected $table = 'stok';

    public $timestamps = true;
    const CREATED_AT = null;
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'barang_id',
        'stok_baik',
        'stok_rusak',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
