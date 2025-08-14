<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_divisi',
        'kode_divisi',
        'kebun_id',
        'luas_divisi',
        'is_active'
    ];

    protected $casts = [
        'luas_divisi' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function kebun()
    {
        return $this->belongsTo(Kebun::class);
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
