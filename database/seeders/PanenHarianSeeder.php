<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PanenHarian;
use App\Models\Kebun;
use App\Models\Divisi;
use Carbon\Carbon;

class PanenHarianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kebuns = Kebun::with('divisis')->get();
        // Kurangi data dummy: hanya 5 hari terakhir dan 1 kebun/divisi saja
        $maxHari = 5;
        $kebun = $kebuns->first();
        $divisi = $kebun ? $kebun->divisis->first() : null;
        if ($kebun && $divisi) {
            for ($i = $maxHari - 1; $i >= 0; $i--) {
                $tanggal = Carbon::now()->subDays($i);
                $luasPanen = rand(50, 200) / 10;
                $jjgPanen = rand(800, 2500);
                $timbangKebun = $jjgPanen * (rand(180, 250) / 10);
                $timbangPks = $timbangKebun * (rand(95, 105) / 100);
                $jumlahTk = rand(15, 50);
                $refraksiKg = $timbangKebun * (rand(1, 5) / 100);
                $alokasiBudget = $timbangPks * (rand(80, 120) / 100);
                PanenHarian::create([
                    'tanggal_panen' => $tanggal,
                    'bulan' => $tanggal->format('F'),
                    'tahun' => $tanggal->year,
                    'kebun' => $kebun->nama_kebun,
                    'divisi' => $divisi->nama_divisi,
                    'akp_panen' => null,
                    'jumlah_tk_panen' => $jumlahTk,
                    'luas_panen_ha' => $luasPanen,
                    'jjg_panen_jjg' => $jjgPanen,
                    'jjg_kirim_jjg' => 0,
                    'ketrek' => null,
                    'total_jjg_kirim_jjg' => 0,
                    'tonase_panen_kg' => $timbangKebun,
                    'refraksi_kg' => $refraksiKg,
                    'refraksi_persen' => 0,
                    'restant_jjg' => 0,
                    'bjr_hari_ini' => 0,
                    'output_kg_hk' => 0,
                    'output_ha_hk' => 0,
                    'budget_harian' => $alokasiBudget,
                    'timbang_kebun_harian' => $timbangKebun,
                    'timbang_pks_harian' => $timbangPks,
                    'rotasi_panen' => 0,
                    'input_by' => 'Seeder',
                    'additional_data' => null,
                ]);
            }
        }
    }
}
