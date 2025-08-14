<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterData;

class NewMasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $masterData = [
            // PT. PAL DIVISI 1
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 682.27, 'budget_alokasi' => 1084317, 'pkk' => 93383, 'bulan' => 'January', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 682.27, 'budget_alokasi' => 1084317, 'pkk' => 93383, 'bulan' => 'February', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 682.27, 'budget_alokasi' => 1084317, 'pkk' => 93383, 'bulan' => 'March', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 682.27, 'budget_alokasi' => 1084317, 'pkk' => 93383, 'bulan' => 'April', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 682.27, 'budget_alokasi' => 1265036, 'pkk' => 93383, 'bulan' => 'May', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 682.27, 'budget_alokasi' => 1445756, 'pkk' => 93383, 'bulan' => 'June', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 682.27, 'budget_alokasi' => 1445756, 'pkk' => 93383, 'bulan' => 'July', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 682.27, 'budget_alokasi' => 1626475, 'pkk' => 93383, 'bulan' => 'August', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 682.27, 'budget_alokasi' => 1807195, 'pkk' => 93383, 'bulan' => 'September', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 682.27, 'budget_alokasi' => 2168633, 'pkk' => 93383, 'bulan' => 'October', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 682.27, 'budget_alokasi' => 2168633, 'pkk' => 93383, 'bulan' => 'November', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 682.27, 'budget_alokasi' => 1807195, 'pkk' => 93383, 'bulan' => 'December', 'tahun' => 2025],

            // PT. PAL DIVISI 2
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 2', 'sph_panen' => 144, 'luas_tm' => 543.35, 'budget_alokasi' => 912798, 'pkk' => 78104, 'bulan' => 'January', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 2', 'sph_panen' => 144, 'luas_tm' => 543.35, 'budget_alokasi' => 912798, 'pkk' => 78104, 'bulan' => 'February', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 2', 'sph_panen' => 144, 'luas_tm' => 543.35, 'budget_alokasi' => 912798, 'pkk' => 78104, 'bulan' => 'March', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 2', 'sph_panen' => 144, 'luas_tm' => 543.35, 'budget_alokasi' => 912798, 'pkk' => 78104, 'bulan' => 'April', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 2', 'sph_panen' => 144, 'luas_tm' => 543.35, 'budget_alokasi' => 1064930, 'pkk' => 78104, 'bulan' => 'May', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 2', 'sph_panen' => 144, 'luas_tm' => 543.35, 'budget_alokasi' => 1217063, 'pkk' => 78104, 'bulan' => 'June', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 2', 'sph_panen' => 144, 'luas_tm' => 543.35, 'budget_alokasi' => 1217063, 'pkk' => 78104, 'bulan' => 'July', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 2', 'sph_panen' => 144, 'luas_tm' => 543.35, 'budget_alokasi' => 1369196, 'pkk' => 78104, 'bulan' => 'August', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 2', 'sph_panen' => 144, 'luas_tm' => 543.35, 'budget_alokasi' => 1521329, 'pkk' => 78104, 'bulan' => 'September', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 2', 'sph_panen' => 144, 'luas_tm' => 543.35, 'budget_alokasi' => 1825595, 'pkk' => 78104, 'bulan' => 'October', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 2', 'sph_panen' => 144, 'luas_tm' => 543.35, 'budget_alokasi' => 1825595, 'pkk' => 78104, 'bulan' => 'November', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 2', 'sph_panen' => 144, 'luas_tm' => 543.35, 'budget_alokasi' => 1521329, 'pkk' => 78104, 'bulan' => 'December', 'tahun' => 2025],

            // PT. PAL DIVISI 3
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 3', 'sph_panen' => 141, 'luas_tm' => 549.73, 'budget_alokasi' => 855055, 'pkk' => 77548, 'bulan' => 'January', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 3', 'sph_panen' => 141, 'luas_tm' => 549.73, 'budget_alokasi' => 855055, 'pkk' => 77548, 'bulan' => 'February', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 3', 'sph_panen' => 141, 'luas_tm' => 549.73, 'budget_alokasi' => 855055, 'pkk' => 77548, 'bulan' => 'March', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 3', 'sph_panen' => 141, 'luas_tm' => 549.73, 'budget_alokasi' => 855055, 'pkk' => 77548, 'bulan' => 'April', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 3', 'sph_panen' => 141, 'luas_tm' => 549.73, 'budget_alokasi' => 997564, 'pkk' => 77548, 'bulan' => 'May', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 3', 'sph_panen' => 141, 'luas_tm' => 549.73, 'budget_alokasi' => 1140073, 'pkk' => 77548, 'bulan' => 'June', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 3', 'sph_panen' => 141, 'luas_tm' => 549.73, 'budget_alokasi' => 1140073, 'pkk' => 77548, 'bulan' => 'July', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 3', 'sph_panen' => 141, 'luas_tm' => 549.73, 'budget_alokasi' => 1282582, 'pkk' => 77548, 'bulan' => 'August', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 3', 'sph_panen' => 141, 'luas_tm' => 549.73, 'budget_alokasi' => 1425091, 'pkk' => 77548, 'bulan' => 'September', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 3', 'sph_panen' => 141, 'luas_tm' => 549.73, 'budget_alokasi' => 1710110, 'pkk' => 77548, 'bulan' => 'October', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 3', 'sph_panen' => 141, 'luas_tm' => 549.73, 'budget_alokasi' => 1710110, 'pkk' => 77548, 'bulan' => 'November', 'tahun' => 2025],
            ['kebun' => 'PT. PAL', 'divisi' => 'DIVISI 3', 'sph_panen' => 141, 'luas_tm' => 549.73, 'budget_alokasi' => 1425091, 'pkk' => 77548, 'bulan' => 'December', 'tahun' => 2025],
        ];

        foreach ($masterData as $data) {
            MasterData::create($data);
        }
    }
}
