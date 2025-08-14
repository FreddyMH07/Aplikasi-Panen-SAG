<?php

namespace App\Exports;

use App\Models\MasterData;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MasterDataExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = MasterData::query();

        // Apply filters
        if (!empty($this->filters['tahun'])) {
            $query->where('tahun', $this->filters['tahun']);
        }

        if (!empty($this->filters['bulan'])) {
            $query->where('bulan', $this->filters['bulan']);
        }

        if (!empty($this->filters['kebun'])) {
            $query->where('kebun', 'like', '%' . $this->filters['kebun'] . '%');
        }

        return $query->orderBy('tahun', 'desc')
                     ->orderBy('bulan')
                     ->orderBy('kebun')
                     ->orderBy('divisi');
    }

    public function headings(): array
    {
        return [
            'Kebun',
            'Divisi',
            'SPH Panen',
            'Luas TM (Ha)',
            'Budget Alokasi',
            'PKK',
            'Bulan',
            'Tahun',
            'Status',
            'Dibuat',
            'Diperbarui'
        ];
    }

    public function map($masterData): array
    {
        return [
            $masterData->kebun,
            $masterData->divisi,
            $masterData->sph_panen,
            $masterData->luas_tm,
            $masterData->budget_alokasi,
            $masterData->pkk,
            $masterData->nama_bulan_indonesia,
            $masterData->tahun,
            $masterData->is_active ? 'Aktif' : 'Tidak Aktif',
            $masterData->created_at->format('d/m/Y H:i'),
            $masterData->updated_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
