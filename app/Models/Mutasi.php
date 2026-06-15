<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mutasi extends Model
{
    use HasFactory;

    protected $table = 'mutasi';

    protected $fillable = [
        'no_mutasi',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(MutasiDetail::class);
    }
}
