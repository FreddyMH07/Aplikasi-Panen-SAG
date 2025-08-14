<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanenBulanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun',
        'bulan',
        'kebun_id',
        'divisi_id',
        'total_luas_panen',
        'total_jjg_panen',
        'total_timbang_kebun',
        'total_timbang_pks',
        'total_jumlah_tk',
        'total_refraksi_kg',
        'total_alokasi_budget',
        'bjr_bulanan',
        'akp_bulanan',
        'acv_prod_bulanan',
        'refraksi_persen'
    ];

    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'total_luas_panen' => 'decimal:2',
        'total_jjg_panen' => 'integer',
        'total_timbang_kebun' => 'decimal:2',
        'total_timbang_pks' => 'decimal:2',
        'total_jumlah_tk' => 'integer',
        'total_refraksi_kg' => 'decimal:2',
        'total_alokasi_budget' => 'decimal:2',
        'bjr_bulanan' => 'decimal:2',
        'akp_bulanan' => 'decimal:4',
        'acv_prod_bulanan' => 'decimal:2',
        'refraksi_persen' => 'decimal:2'
    ];

    public function kebun()
    {
        return $this->belongsTo(Kebun::class);
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    public function getSelisihAttribute()
    {
        return round($this->total_timbang_pks - $this->total_timbang_kebun, 2);
    }

    public function getNamaBulanAttribute()
    {
        $bulanIndonesia = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulanIndonesia[$this->bulan] ?? '';
    }
}
