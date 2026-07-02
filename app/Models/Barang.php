<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'satuan',
        'harga_gold',
        'harga_grosir',
        'harga_khusus',
    ];

    public function stok(): HasOne
    {
        return $this->hasOne(Stok::class);
    }

    public function initialStok(): HasOne
    {
        return $this->hasOne(InitialStok::class);
    }

    public function barangMasukDetails(): HasMany
    {
        return $this->hasMany(BarangMasukDetail::class);
    }

    public function barangKeluarDetails(): HasMany
    {
        return $this->hasMany(BarangKeluarDetail::class);
    }

    public function mutasiDetails(): HasMany
    {
        return $this->hasMany(MutasiDetail::class);
    }

    public function penyesuaians(): HasMany
    {
        return $this->hasMany(PenyesuaianStok::class);
    }
}
