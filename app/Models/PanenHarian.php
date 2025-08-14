<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PanenHarian extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal_panen',
        'bulan',
        'tahun',
        'kebun',
        'divisi',
        'akp_panen',
        'jumlah_tk_panen',
        'luas_panen_ha',
        'jjg_panen_jjg',
        'jjg_kirim_jjg',
        'ketrek',
        'total_jjg_kirim_jjg',
        'tonase_panen_kg',
        'refraksi_kg',
        'refraksi_persen',
        'restant_jjg',
        'bjr_hari_ini',
        'output_kg_hk',
        'output_ha_hk',
        'budget_harian',
        'timbang_kebun_harian',
        'timbang_pks_harian',
        'rotasi_panen',
        'input_by',
        'additional_data'
    ];

    protected $casts = [
        'tanggal_panen' => 'date',
        'tahun' => 'integer',
        'jumlah_tk_panen' => 'integer',
        'luas_panen_ha' => 'float',
        'jjg_panen_jjg' => 'integer',
        'jjg_kirim_jjg' => 'integer',
        'total_jjg_kirim_jjg' => 'integer',
        'tonase_panen_kg' => 'float',
        'refraksi_kg' => 'float',
        'refraksi_persen' => 'float',
        'restant_jjg' => 'integer',
        'bjr_hari_ini' => 'float',
        'output_kg_hk' => 'float',
        'output_ha_hk' => 'float',
        'budget_harian' => 'float',
        'timbang_kebun_harian' => 'float',
        'timbang_pks_harian' => 'float',
        'rotasi_panen' => 'float',
        'ketrek' => 'float',
        'additional_data' => 'array'
    ];

    // Relasi ke master data
    public function masterData()
    {
        return $this->hasOne(MasterData::class, function($query) {
            $query->where('kebun', $this->kebun)
                  ->where('divisi', $this->divisi)
                  ->where('tahun', $this->tahun)
                  ->where('bulan', $this->bulan);
        });
    }

    // Calculated attributes
    public function getBjrAttribute()
    {
        return $this->jjg_panen_jjg > 0 ? round($this->timbang_kebun_harian / $this->jjg_panen_jjg, 2) : 0;
    }

    public function getAkpCalculatedAttribute()
    {
        $masterData = MasterData::getByKebunDivisi($this->kebun, $this->divisi, $this->tahun, $this->bulan);
        $sph = $masterData ? $masterData->sph_panen : 136;
        
        return ($this->luas_panen_ha * $sph) > 0 ? 
            round($this->jjg_panen_jjg / ($this->luas_panen_ha * $sph), 4) : 0;
    }

    public function getAcvProdAttribute()
    {
        return $this->budget_harian > 0 ? 
            round(100 * $this->timbang_pks_harian / $this->budget_harian, 2) : 0;
    }

    public function getSelisihAttribute()
    {
        return round($this->timbang_pks_harian - $this->timbang_kebun_harian, 2);
    }

    public function getRefraksiPersenCalculatedAttribute()
    {
        return $this->tonase_panen_kg > 0 ? 
            round(100 * $this->refraksi_kg / $this->tonase_panen_kg, 2) : 0;
    }

    // Scope untuk filter
    public function scopeByPeriode($query, $tahun, $bulan = null)
    {
        $query->where('tahun', $tahun);
        if ($bulan) {
            $query->where('bulan', $bulan);
        }
        return $query;
    }

    public function scopeByKebun($query, $kebun)
    {
        return $query->where('kebun', $kebun);
    }

    public function scopeByDivisi($query, $divisi)
    {
        return $query->where('divisi', $divisi);
    }

    // Auto-fill bulan dan tahun dari tanggal
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if ($model->tanggal_panen) {
                $date = Carbon::parse($model->tanggal_panen);
                $model->bulan = $date->format('F'); // January, February, etc.
                $model->tahun = $date->year;
            }
            
            if (auth()->check()) {
                $model->input_by = auth()->user()->name;
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('tanggal_panen')) {
                $date = Carbon::parse($model->tanggal_panen);
                $model->bulan = $date->format('F');
                $model->tahun = $date->year;
            }
        });
    }
}
