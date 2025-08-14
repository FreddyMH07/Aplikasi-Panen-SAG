<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterData extends Model
{
    use HasFactory;

    protected $table = 'master_data';

    protected $fillable = [
        'kebun',
        'divisi',
        'sph_panen',
        'luas_tm',
        'budget_alokasi',
        'pkk',
        'bulan',
        'tahun',
        'is_active'
    ];

    protected $casts = [
        'sph_panen' => 'float',
        'luas_tm' => 'float',
        'budget_alokasi' => 'float',
        'pkk' => 'integer',
        'tahun' => 'integer',
        'is_active' => 'boolean'
    ];

    // Scope untuk filter aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk filter berdasarkan periode
    public function scopePeriode($query, $tahun, $bulan = null)
    {
        $query->where('tahun', $tahun);
        if ($bulan) {
            $query->where('bulan', $bulan);
        }
        return $query;
    }

    // Accessor untuk nama bulan Indonesia
    public function getNamaBulanIndonesiaAttribute()
    {
        $bulanMap = [
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
            'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
        ];
        
        return $bulanMap[$this->bulan] ?? $this->bulan;
    }

    // Method untuk mendapatkan data berdasarkan kebun dan divisi
    public static function getByKebunDivisi($kebun, $divisi, $tahun, $bulan)
    {
        return self::where('kebun', $kebun)
                   ->where('divisi', $divisi)
                   ->where('tahun', $tahun)
                   ->where('bulan', $bulan)
                   ->first();
    }
}
