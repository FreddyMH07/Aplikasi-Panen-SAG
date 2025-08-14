<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kebun extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kebun',
        'kode_kebun',
        'alamat',
        'luas_total',
        'sph_panen',
        'is_active'
    ];

    protected $casts = [
        'luas_total' => 'decimal:2',
        'sph_panen' => 'integer',
        'is_active' => 'boolean'
    ];

    public function divisis()
    {
        return $this->hasMany(Divisi::class);
    }

    public function panenHarians()
    {
        return $this->hasMany(PanenHarian::class);
    }

    public function panenBulanans()
    {
        return $this->hasMany(PanenBulanan::class);
    }
}
