<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MasterData;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kebuns = ['Kebun Sawit Utama', 'Kebun Sawit Selatan', 'Kebun Sawit Timur'];
        $divisis = ['Divisi A', 'Divisi B', 'Divisi C', 'Divisi D'];
        $bulanList = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        
        $currentYear = date('Y');
        $lastYear = $currentYear - 1;
        
        foreach ([$lastYear, $currentYear] as $tahun) {
            foreach ($bulanList as $bulan) {
                foreach ($kebuns as $kebun) {
                    foreach ($divisis as $divisi) {
                        MasterData::create([
                            'kebun' => $kebun,
                            'divisi' => $divisi,
                            'sph_panen' => rand(130, 145), // SPH bervariasi 130-145
                            'luas_tm' => rand(200, 800) / 10, // 20-80 Ha
                            'budget_alokasi' => rand(50000000, 200000000), // 50-200 juta
                            'pkk' => rand(2000, 8000), // 2000-8000 pokok
                            'bulan' => $bulan,
                            'tahun' => $tahun,
                            'is_active' => true
                        ]);
                    }
                }
            }
        }
    }
}
