<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Toko extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'toko';

    protected $fillable = [
        'kode_toko',
        'nama_toko',
        'alamat',
    ];

    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class);
    }

    public function barangKeluars()
    {
        return $this->hasMany(BarangKeluar::class);
    }
}
