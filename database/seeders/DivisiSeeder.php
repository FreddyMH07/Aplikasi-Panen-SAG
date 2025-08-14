<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Divisi;
use App\Models\Kebun;

class DivisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kebuns = Kebun::all();

        foreach ($kebuns as $kebun) {
            // Setiap kebun memiliki 3-4 divisi
            $divisis = [
                [
                    'nama_divisi' => 'Divisi A',
                    'kode_divisi' => $kebun->kode_kebun . '-A',
                    'kebun_id' => $kebun->id,
                    'luas_divisi' => $kebun->luas_total * 0.3,
                    'is_active' => true
                ],
                [
                    'nama_divisi' => 'Divisi B',
                    'kode_divisi' => $kebun->kode_kebun . '-B',
                    'kebun_id' => $kebun->id,
                    'luas_divisi' => $kebun->luas_total * 0.25,
                    'is_active' => true
                ],
                [
                    'nama_divisi' => 'Divisi C',
                    'kode_divisi' => $kebun->kode_kebun . '-C',
                    'kebun_id' => $kebun->id,
                    'luas_divisi' => $kebun->luas_total * 0.25,
                    'is_active' => true
                ],
                [
                    'nama_divisi' => 'Divisi D',
                    'kode_divisi' => $kebun->kode_kebun . '-D',
                    'kebun_id' => $kebun->id,
                    'luas_divisi' => $kebun->luas_total * 0.2,
                    'is_active' => true
                ]
            ];

            foreach ($divisis as $divisi) {
                Divisi::create($divisi);
            }
        }
    }
}
