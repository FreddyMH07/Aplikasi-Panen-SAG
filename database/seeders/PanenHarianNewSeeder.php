<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PanenHarian;
use App\Models\MasterData;
use Carbon\Carbon;

class PanenHarianNewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kebuns = ['Kebun Sawit Utama', 'Kebun Sawit Selatan', 'Kebun Sawit Timur'];
        $divisis = ['Divisi A', 'Divisi B', 'Divisi C', 'Divisi D'];
        
        // Generate data untuk 60 hari terakhir
        for ($i = 59; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subDays($i);
            $bulan = $tanggal->format('F');
            $tahun = $tanggal->year;
            
            foreach ($kebuns as $kebun) {
                foreach ($divisis as $divisi) {
                    // Skip beberapa hari secara random untuk variasi data
                    if (rand(1, 10) <= 2) continue;
                    
                    // Get master data untuk referensi
                    $masterData = MasterData::getByKebunDivisi($kebun, $divisi, $tahun, $bulan);
                    $sph = $masterData ? $masterData->sph_panen : 136;
                    $budgetHarian = $masterData ? ($masterData->budget_alokasi / 30) : rand(1000000, 5000000);
                    
                    // Generate realistic data
                    $luasPanenHa = rand(50, 200) / 10; // 5.0 - 20.0 Ha
                    $jjgPanenJjg = rand(800, 2500);
                    $jjgKirimJjg = $jjgPanenJjg - rand(0, 50); // Sedikit kurang dari panen
                    $totalJjgKirimJjg = $jjgKirimJjg;
                    $tonasePanenKg = $jjgPanenJjg * (rand(180, 250) / 10); // 18-25 kg per JJG
                    $timbangKebunHarian = $tonasePanenKg * (rand(95, 105) / 100);
                    $timbangPksHarian = $timbangKebunHarian * (rand(95, 105) / 100);
                    $jumlahTkPanen = rand(15, 50);
                    $refraksiKg = $tonasePanenKg * (rand(1, 5) / 100); // 1-5% refraksi
                    $refraksiPersen = $tonasePanenKg > 0 ? ($refraksiKg / $tonasePanenKg * 100) : 0;
                    $restantJjg = rand(0, 20);
                    $bjrHariIni = $jjgPanenJjg > 0 ? ($timbangKebunHarian / $jjgPanenJjg) : 0;
                    $outputKgHk = $jumlahTkPanen > 0 ? ($tonasePanenKg / $jumlahTkPanen) : 0;
                    $outputHaHk = $jumlahTkPanen > 0 ? ($luasPanenHa / $jumlahTkPanen) : 0;
                    $rotasiPanen = rand(7, 14); // 7-14 hari rotasi
                    $akpPanen = ($luasPanenHa * $sph) > 0 ? 
                        number_format(($jjgPanenJjg / ($luasPanenHa * $sph)) * 100, 2) . '%' : '0%';
                    
                    PanenHarian::create([
                        'tanggal_panen' => $tanggal,
                        'bulan' => $bulan,
                        'tahun' => $tahun,
                        'kebun' => $kebun,
                        'divisi' => $divisi,
                        'akp_panen' => $akpPanen,
                        'jumlah_tk_panen' => $jumlahTkPanen,
                        'luas_panen_ha' => $luasPanenHa,
                        'jjg_panen_jjg' => $jjgPanenJjg,
                        'jjg_kirim_jjg' => $jjgKirimJjg,
                        'ketrek' => rand(1, 10) <= 2 ? 'Ketrek-' . rand(1, 5) : null,
                        'total_jjg_kirim_jjg' => $totalJjgKirimJjg,
                        'tonase_panen_kg' => $tonasePanenKg,
                        'refraksi_kg' => $refraksiKg,
                        'refraksi_persen' => $refraksiPersen,
                        'restant_jjg' => $restantJjg,
                        'bjr_hari_ini' => $bjrHariIni,
                        'output_kg_hk' => $outputKgHk,
                        'output_ha_hk' => $outputHaHk,
                        'budget_harian' => $budgetHarian,
                        'timbang_kebun_harian' => $timbangKebunHarian,
                        'timbang_pks_harian' => $timbangPksHarian,
                        'rotasi_panen' => $rotasiPanen,
                        'input_by' => 'System Seeder'
                    ]);
                }
            }
        }
    }
}
