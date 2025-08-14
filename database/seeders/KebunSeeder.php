<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kebun;

class KebunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kebuns = [
            [
                'nama_kebun' => 'Kebun Sawit Utama',
                'kode_kebun' => 'KSU001',
                'alamat' => 'Jl. Perkebunan No. 1, Riau',
                'luas_total' => 1500.50,
                'sph_panen' => 136,
                'is_active' => true
            ],
            [
                'nama_kebun' => 'Kebun Sawit Selatan',
                'kode_kebun' => 'KSS002',
                'alamat' => 'Jl. Perkebunan No. 2, Jambi',
                'luas_total' => 2200.75,
                'sph_panen' => 140,
                'is_active' => true
            ],
            [
                'nama_kebun' => 'Kebun Sawit Timur',
                'kode_kebun' => 'KST003',
                'alamat' => 'Jl. Perkebunan No. 3, Kalimantan',
                'luas_total' => 1800.25,
                'sph_panen' => 138,
                'is_active' => true
            ]
        ];

        foreach ($kebuns as $kebun) {
            Kebun::create($kebun);
        }
    }
}
