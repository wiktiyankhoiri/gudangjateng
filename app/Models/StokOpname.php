<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StokOpname extends Model
{
    use HasFactory;

    protected $table = 'stok_opname';

    protected $fillable = [
        'no_opname',
        'tanggal_opname',
        'status',
        'catatan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_opname' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(StokOpnameDetail::class);
    }
}
