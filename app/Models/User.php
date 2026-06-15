<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    // public $timestamps = false; // Diaktifkan agar updated_at terisi otomatis

    protected $fillable = [
        'nama',
        'username',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'created_at' => 'datetime',
        ];
    }

    public function appNotifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    public function penyesuaians()
    {
        return $this->hasMany(\App\Models\PenyesuaianStok::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(\App\Models\AuditLog::class);
    }

    public function barangKeluars()
    {
        return $this->hasMany(\App\Models\BarangKeluar::class, 'sales_id');
    }
}
