<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TableColumn;

class TableColumnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kolom untuk tabel panen_harian
        $panenHarianColumns = [
            [
                'table_name' => 'panen_harian',
                'column_name' => 'tanggal_panen',
                'column_label' => 'Tanggal Panen',
                'column_type' => 'date',
                'is_visible' => true,
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'table_name' => 'panen_harian',
                'column_name' => 'kebun_id',
                'column_label' => 'Kebun',
                'column_type' => 'select',
                'is_visible' => true,
                'is_required' => true,
                'sort_order' => 2
            ],
            [
                'table_name' => 'panen_harian',
                'column_name' => 'divisi_id',
                'column_label' => 'Divisi',
                'column_type' => 'select',
                'is_visible' => true,
                'is_required' => true,
                'sort_order' => 3
            ],
            [
                'table_name' => 'panen_harian',
                'column_name' => 'luas_panen',
                'column_label' => 'Luas Panen (Ha)',
                'column_type' => 'number',
                'is_visible' => true,
                'is_required' => true,
                'sort_order' => 4
            ],
            [
                'table_name' => 'panen_harian',
                'column_name' => 'jjg_panen',
                'column_label' => 'JJG Panen',
                'column_type' => 'number',
                'is_visible' => true,
                'is_required' => true,
                'sort_order' => 5
            ],
            [
                'table_name' => 'panen_harian',
                'column_name' => 'timbang_kebun',
                'column_label' => 'Timbang Kebun (Kg)',
                'column_type' => 'number',
                'is_visible' => true,
                'is_required' => true,
                'sort_order' => 6
            ],
            [
                'table_name' => 'panen_harian',
                'column_name' => 'timbang_pks',
                'column_label' => 'Timbang PKS (Kg)',
                'column_type' => 'number',
                'is_visible' => true,
                'is_required' => true,
                'sort_order' => 7
            ],
            [
                'table_name' => 'panen_harian',
                'column_name' => 'jumlah_tk',
                'column_label' => 'HK (Tenaga Kerja)',
                'column_type' => 'number',
                'is_visible' => true,
                'is_required' => true,
                'sort_order' => 8
            ],
            [
                'table_name' => 'panen_harian',
                'column_name' => 'refraksi_kg',
                'column_label' => 'Refraksi (Kg)',
                'column_type' => 'number',
                'is_visible' => true,
                'is_required' => false,
                'sort_order' => 9
            ],
            [
                'table_name' => 'panen_harian',
                'column_name' => 'alokasi_budget',
                'column_label' => 'Alokasi Budget',
                'column_type' => 'number',
                'is_visible' => true,
                'is_required' => false,
                'sort_order' => 10
            ]
        ];

        foreach ($panenHarianColumns as $column) {
            TableColumn::create($column);
        }

        // Kolom untuk tabel panen_bulanan
        $panenBulananColumns = [
            [
                'table_name' => 'panen_bulanan',
                'column_name' => 'tahun',
                'column_label' => 'Tahun',
                'column_type' => 'number',
                'is_visible' => true,
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'table_name' => 'panen_bulanan',
                'column_name' => 'bulan',
                'column_label' => 'Bulan',
                'column_type' => 'select',
                'is_visible' => true,
                'is_required' => true,
                'sort_order' => 2
            ],
            [
                'table_name' => 'panen_bulanan',
                'column_name' => 'kebun_id',
                'column_label' => 'Kebun',
                'column_type' => 'select',
                'is_visible' => true,
                'is_required' => true,
                'sort_order' => 3
            ],
            [
                'table_name' => 'panen_bulanan',
                'column_name' => 'divisi_id',
                'column_label' => 'Divisi',
                'column_type' => 'select',
                'is_visible' => true,
                'is_required' => true,
                'sort_order' => 4
            ]
        ];

        foreach ($panenBulananColumns as $column) {
            TableColumn::create($column);
        }
    }
}
