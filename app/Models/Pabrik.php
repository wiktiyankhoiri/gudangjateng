<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pabrik extends Model
{
    use HasFactory;

    protected $table = 'pabrik';

    protected $fillable = [
        'kode_pabrik',
        'nama_pabrik',
        'alamat',
    ];

    public function barangMasuks(): HasMany
    {
        return $this->hasMany(BarangMasuk::class);
    }
}
