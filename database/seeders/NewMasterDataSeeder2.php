<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterData;

class NewMasterDataSeeder2 extends Seeder
{
    public function run(): void
    {
        $masterData = [
            // PT. LSP RS DIVISI 1
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 168.51, 'budget_alokasi' => 173287, 'pkk' => 23003, 'bulan' => 'January', 'tahun' => 2025],
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 168.51, 'budget_alokasi' => 147058, 'pkk' => 23003, 'bulan' => 'February', 'tahun' => 2025],
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 168.51, 'budget_alokasi' => 120830, 'pkk' => 23003, 'bulan' => 'March', 'tahun' => 2025],
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 168.51, 'budget_alokasi' => 173287, 'pkk' => 23003, 'bulan' => 'April', 'tahun' => 2025],
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 168.51, 'budget_alokasi' => 199515, 'pkk' => 23003, 'bulan' => 'May', 'tahun' => 2025],
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 168.51, 'budget_alokasi' => 251972, 'pkk' => 23003, 'bulan' => 'June', 'tahun' => 2025],
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 168.51, 'budget_alokasi' => 255155, 'pkk' => 23003, 'bulan' => 'July', 'tahun' => 2025],
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 168.51, 'budget_alokasi' => 310796, 'pkk' => 23003, 'bulan' => 'August', 'tahun' => 2025],
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 168.51, 'budget_alokasi' => 276544, 'pkk' => 23003, 'bulan' => 'September', 'tahun' => 2025],
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 168.51, 'budget_alokasi' => 276544, 'pkk' => 23003, 'bulan' => 'October', 'tahun' => 2025],
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 168.51, 'budget_alokasi' => 230453, 'pkk' => 23003, 'bulan' => 'November', 'tahun' => 2025],
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 1', 'sph_panen' => 137, 'luas_tm' => 168.51, 'budget_alokasi' => 207408, 'pkk' => 23003, 'bulan' => 'December', 'tahun' => 2025],

            // PT. LSP RS DIVISI 2
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 2', 'sph_panen' => 134, 'luas_tm' => 500.25, 'budget_alokasi' => 456040, 'pkk' => 66862, 'bulan' => 'January', 'tahun' => 2025],
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 2', 'sph_panen' => 134, 'luas_tm' => 500.25, 'budget_alokasi' => 380033, 'pkk' => 66862, 'bulan' => 'February', 'tahun' => 2025],
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 2', 'sph_panen' => 134, 'luas_tm' => 500.25, 'budget_alokasi' => 304027, 'pkk' => 66862, 'bulan' => 'March', 'tahun' => 2025],
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 2', 'sph_panen' => 134, 'luas_tm' => 500.25, 'budget_alokasi' => 456040, 'pkk' => 66862, 'bulan' => 'April', 'tahun' => 2025],
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 2', 'sph_panen' => 134, 'luas_tm' => 500.25, 'budget_alokasi' => 532047, 'pkk' => 66862, 'bulan' => 'May', 'tahun' => 2025],
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 2', 'sph_panen' => 134, 'luas_tm' => 500.25, 'budget_alokasi' => 684060, 'pkk' => 66862, 'bulan' => 'June', 'tahun' => 2025],
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 2', 'sph_panen' => 134, 'luas_tm' => 500.25, 'budget_alokasi' => 684060, 'pkk' => 66862, 'bulan' => 'July', 'tahun' => 2025],
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 2', 'sph_panen' => 134, 'luas_tm' => 500.25, 'budget_alokasi' => 836074, 'pkk' => 66862, 'bulan' => 'August', 'tahun' => 2025],
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 2', 'sph_panen' => 134, 'luas_tm' => 500.25, 'budget_alokasi' => 912080, 'pkk' => 66862, 'bulan' => 'September', 'tahun' => 2025],
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 2', 'sph_panen' => 134, 'luas_tm' => 500.25, 'budget_alokasi' => 912080, 'pkk' => 66862, 'bulan' => 'October', 'tahun' => 2025],
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 2', 'sph_panen' => 134, 'luas_tm' => 500.25, 'budget_alokasi' => 760067, 'pkk' => 66862, 'bulan' => 'November', 'tahun' => 2025],
            ['kebun' => 'PT. LSP RS', 'divisi' => 'DIVISI 2', 'sph_panen' => 134, 'luas_tm' => 500.25, 'budget_alokasi' => 684060, 'pkk' => 66862, 'bulan' => 'December', 'tahun' => 2025],

            // PT. LSP PR DIVISI 1
            ['kebun' => 'PT. LSP PR', 'divisi' => 'DIVISI 1', 'sph_panen' => 146, 'luas_tm' => 196.16, 'budget_alokasi' => 303581, 'pkk' => 28726, 'bulan' => 'January', 'tahun' => 2025],
            ['kebun' => 'PT. LSP PR', 'divisi' => 'DIVISI 1', 'sph_panen' => 146, 'luas_tm' => 196.16, 'budget_alokasi' => 252985, 'pkk' => 28726, 'bulan' => 'February', 'tahun' => 2025],
            ['kebun' => 'PT. LSP PR', 'divisi' => 'DIVISI 1', 'sph_panen' => 146, 'luas_tm' => 196.16, 'budget_alokasi' => 202388, 'pkk' => 28726, 'bulan' => 'March', 'tahun' => 2025],
            ['kebun' => 'PT. LSP PR', 'divisi' => 'DIVISI 1', 'sph_panen' => 146, 'luas_tm' => 196.16, 'budget_alokasi' => 303581, 'pkk' => 28726, 'bulan' => 'April', 'tahun' => 2025],
            ['kebun' => 'PT. LSP PR', 'divisi' => 'DIVISI 1', 'sph_panen' => 146, 'luas_tm' => 196.16, 'budget_alokasi' => 354178, 'pkk' => 28726, 'bulan' => 'May', 'tahun' => 2025],
            ['kebun' => 'PT. LSP PR', 'divisi' => 'DIVISI 1', 'sph_panen' => 146, 'luas_tm' => 196.16, 'budget_alokasi' => 455372, 'pkk' => 28726, 'bulan' => 'June', 'tahun' => 2025],
            ['kebun' => 'PT. LSP PR', 'divisi' => 'DIVISI 1', 'sph_panen' => 146, 'luas_tm' => 196.16, 'budget_alokasi' => 455372, 'pkk' => 28726, 'bulan' => 'July', 'tahun' => 2025],
            ['kebun' => 'PT. LSP PR', 'divisi' => 'DIVISI 1', 'sph_panen' => 146, 'luas_tm' => 196.16, 'budget_alokasi' => 556566, 'pkk' => 28726, 'bulan' => 'August', 'tahun' => 2025],
            ['kebun' => 'PT. LSP PR', 'divisi' => 'DIVISI 1', 'sph_panen' => 146, 'luas_tm' => 196.16, 'budget_alokasi' => 607163, 'pkk' => 28726, 'bulan' => 'September', 'tahun' => 2025],
            ['kebun' => 'PT. LSP PR', 'divisi' => 'DIVISI 1', 'sph_panen' => 146, 'luas_tm' => 196.16, 'budget_alokasi' => 607163, 'pkk' => 28726, 'bulan' => 'October', 'tahun' => 2025],
            ['kebun' => 'PT. LSP PR', 'divisi' => 'DIVISI 1', 'sph_panen' => 146, 'luas_tm' => 196.16, 'budget_alokasi' => 505969, 'pkk' => 28726, 'bulan' => 'November', 'tahun' => 2025],
            ['kebun' => 'PT. LSP PR', 'divisi' => 'DIVISI 1', 'sph_panen' => 146, 'luas_tm' => 196.16, 'budget_alokasi' => 455372, 'pkk' => 28726, 'bulan' => 'December', 'tahun' => 2025],

            // CANDIMAS DIVISI 1
            ['kebun' => 'CANDIMAS', 'divisi' => 'DIVISI 1', 'sph_panen' => 121, 'luas_tm' => 40.51, 'budget_alokasi' => 42536, 'pkk' => 4912, 'bulan' => 'January', 'tahun' => 2025],
            ['kebun' => 'CANDIMAS', 'divisi' => 'DIVISI 1', 'sph_panen' => 121, 'luas_tm' => 40.51, 'budget_alokasi' => 42536, 'pkk' => 4912, 'bulan' => 'February', 'tahun' => 2025],
            ['kebun' => 'CANDIMAS', 'divisi' => 'DIVISI 1', 'sph_panen' => 121, 'luas_tm' => 40.51, 'budget_alokasi' => 51043, 'pkk' => 4912, 'bulan' => 'March', 'tahun' => 2025],
            ['kebun' => 'CANDIMAS', 'divisi' => 'DIVISI 1', 'sph_panen' => 121, 'luas_tm' => 40.51, 'budget_alokasi' => 59550, 'pkk' => 4912, 'bulan' => 'April', 'tahun' => 2025],
            ['kebun' => 'CANDIMAS', 'divisi' => 'DIVISI 1', 'sph_panen' => 121, 'luas_tm' => 40.51, 'budget_alokasi' => 76564, 'pkk' => 4912, 'bulan' => 'May', 'tahun' => 2025],
            ['kebun' => 'CANDIMAS', 'divisi' => 'DIVISI 1', 'sph_panen' => 121, 'luas_tm' => 40.51, 'budget_alokasi' => 85071, 'pkk' => 4912, 'bulan' => 'June', 'tahun' => 2025],
            ['kebun' => 'CANDIMAS', 'divisi' => 'DIVISI 1', 'sph_panen' => 121, 'luas_tm' => 40.51, 'budget_alokasi' => 93578, 'pkk' => 4912, 'bulan' => 'July', 'tahun' => 2025],
            ['kebun' => 'CANDIMAS', 'divisi' => 'DIVISI 1', 'sph_panen' => 121, 'luas_tm' => 40.51, 'budget_alokasi' => 102085, 'pkk' => 4912, 'bulan' => 'August', 'tahun' => 2025],
            ['kebun' => 'CANDIMAS', 'divisi' => 'DIVISI 1', 'sph_panen' => 121, 'luas_tm' => 40.51, 'budget_alokasi' => 93578, 'pkk' => 4912, 'bulan' => 'September', 'tahun' => 2025],
            ['kebun' => 'CANDIMAS', 'divisi' => 'DIVISI 1', 'sph_panen' => 121, 'luas_tm' => 40.51, 'budget_alokasi' => 76564, 'pkk' => 4912, 'bulan' => 'October', 'tahun' => 2025],
            ['kebun' => 'CANDIMAS', 'divisi' => 'DIVISI 1', 'sph_panen' => 121, 'luas_tm' => 40.51, 'budget_alokasi' => 68057, 'pkk' => 4912, 'bulan' => 'November', 'tahun' => 2025],
            ['kebun' => 'CANDIMAS', 'divisi' => 'DIVISI 1', 'sph_panen' => 121, 'luas_tm' => 40.51, 'budget_alokasi' => 59550, 'pkk' => 4912, 'bulan' => 'December', 'tahun' => 2025],

            // PT. HSBS INTI
            ['kebun' => 'PT. HSBS', 'divisi' => 'INTI', 'sph_panen' => 135, 'luas_tm' => 424.4, 'budget_alokasi' => 599516, 'pkk' => 57265, 'bulan' => 'January', 'tahun' => 2025],
            ['kebun' => 'PT. HSBS', 'divisi' => 'INTI', 'sph_panen' => 135, 'luas_tm' => 424.4, 'budget_alokasi' => 499597, 'pkk' => 57265, 'bulan' => 'February', 'tahun' => 2025],
            ['kebun' => 'PT. HSBS', 'divisi' => 'INTI', 'sph_panen' => 135, 'luas_tm' => 424.4, 'budget_alokasi' => 499597, 'pkk' => 57265, 'bulan' => 'March', 'tahun' => 2025],
            ['kebun' => 'PT. HSBS', 'divisi' => 'INTI', 'sph_panen' => 135, 'luas_tm' => 424.4, 'budget_alokasi' => 599516, 'pkk' => 57265, 'bulan' => 'April', 'tahun' => 2025],
            ['kebun' => 'PT. HSBS', 'divisi' => 'INTI', 'sph_panen' => 135, 'luas_tm' => 424.4, 'budget_alokasi' => 799355, 'pkk' => 57265, 'bulan' => 'May', 'tahun' => 2025],
            ['kebun' => 'PT. HSBS', 'divisi' => 'INTI', 'sph_panen' => 135, 'luas_tm' => 424.4, 'budget_alokasi' => 899274, 'pkk' => 57265, 'bulan' => 'June', 'tahun' => 2025],
            ['kebun' => 'PT. HSBS', 'divisi' => 'INTI', 'sph_panen' => 135, 'luas_tm' => 424.4, 'budget_alokasi' => 999194, 'pkk' => 57265, 'bulan' => 'July', 'tahun' => 2025],
            ['kebun' => 'PT. HSBS', 'divisi' => 'INTI', 'sph_panen' => 135, 'luas_tm' => 424.4, 'budget_alokasi' => 1099113, 'pkk' => 57265, 'bulan' => 'August', 'tahun' => 2025],
            ['kebun' => 'PT. HSBS', 'divisi' => 'INTI', 'sph_panen' => 135, 'luas_tm' => 424.4, 'budget_alokasi' => 1099113, 'pkk' => 57265, 'bulan' => 'September', 'tahun' => 2025],
            ['kebun' => 'PT. HSBS', 'divisi' => 'INTI', 'sph_panen' => 135, 'luas_tm' => 424.4, 'budget_alokasi' => 1099113, 'pkk' => 57265, 'bulan' => 'October', 'tahun' => 2025],
            ['kebun' => 'PT. HSBS', 'divisi' => 'INTI', 'sph_panen' => 135, 'luas_tm' => 424.4, 'budget_alokasi' => 999194, 'pkk' => 57265, 'bulan' => 'November', 'tahun' => 2025],
            ['kebun' => 'PT. HSBS', 'divisi' => 'INTI', 'sph_panen' => 135, 'luas_tm' => 424.4, 'budget_alokasi' => 799355, 'pkk' => 57265, 'bulan' => 'December', 'tahun' => 2025],

            // PT. HSBS PLASMA
            ['kebun' => 'PT. HSBS', 'divisi' => 'PLASMA', 'sph_panen' => 136, 'luas_tm' => 432.05, 'budget_alokasi' => 623610, 'pkk' => 58684, 'bulan' => 'January', 'tahun' => 2025],
            ['kebun' => 'PT. HSBS', 'divisi' => 'PLASMA', 'sph_panen' => 136, 'luas_tm' => 432.05, 'budget_alokasi' => 519675, 'pkk' => 58684, 'bulan' => 'February', 'tahun' => 2025],
            ['kebun' => 'PT. HSBS', 'divisi' => 'PLASMA', 'sph_panen' => 136, 'luas_tm' => 432.05, 'budget_alokasi' => 519675, 'pkk' => 58684, 'bulan' => 'March', 'tahun' => 2025],
            ['kebun' => 'PT. HSBS', 'divisi' => 'PLASMA', 'sph_panen' => 136, 'luas_tm' => 432.05, 'budget_alokasi' => 623610, 'pkk' => 58684, 'bulan' => 'April', 'tahun' => 2025],
            ['kebun' => 'PT. HSBS', 'divisi' => 'PLASMA', 'sph_panen' => 136, 'luas_tm' => 432.05, 'budget_alokasi' => 831480, 'pkk' => 58684, 'bulan' => 'May', 'tahun' => 2025],
            ['kebun' => 'PT. HSBS', 'divisi' => 'PLASMA', 'sph_panen' => 136, 'luas_tm' => 432.05, 'budget_alokasi' => 935415, 'pkk' => 58684, 'bulan' => 'June', 'tahun' => 2025],
            ['kebun' => 'PT. HSBS', 'divisi' => 'PLASMA', 'sph_panen' => 136, 'luas_tm' => 432.05, 'budget_alokasi' => 1039350, 'pkk' => 58684, 'bulan' => 'July', 'tahun' => 2025],
            ['kebun' => 'PT. HSBS', 'divisi' => 'PLASMA', 'sph_panen' => 136, 'luas_tm' => 432.05, 'budget_alokasi' => 1143285, 'pkk' => 58684, 'bulan' => 'August', 'tahun' => 2025],
            ['kebun' => 'PT. HSBS', 'divisi' => 'PLASMA', 'sph_panen' => 136, 'luas_tm' => 432.05, 'budget_alokasi' => 1143285, 'pkk' => 58684, 'bulan' => 'September', 'tahun' => 2025],
            ['kebun' => 'PT. HSBS', 'divisi' => 'PLASMA', 'sph_panen' => 136, 'luas_tm' => 432.05, 'budget_alokasi' => 1143285, 'pkk' => 58684, 'bulan' => 'October', 'tahun' => 2025],
            ['kebun' => 'PT. HSBS', 'divisi' => 'PLASMA', 'sph_panen' => 136, 'luas_tm' => 432.05, 'budget_alokasi' => 1039350, 'pkk' => 58684, 'bulan' => 'November', 'tahun' => 2025],
            ['kebun' => 'PT. HSBS', 'divisi' => 'PLASMA', 'sph_panen' => 136, 'luas_tm' => 432.05, 'budget_alokasi' => 831480, 'pkk' => 58684, 'bulan' => 'December', 'tahun' => 2025],
        ];

        foreach ($masterData as $data) {
            MasterData::create($data);
        }
    }
}
