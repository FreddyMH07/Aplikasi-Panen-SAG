<?php

namespace App\Exports;

use App\Models\PanenHarian;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PanenHarianExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = PanenHarian::query();

        // Apply filters
        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $query->whereBetween('tanggal_panen', [$this->filters['start_date'], $this->filters['end_date']]);
        }

        if (!empty($this->filters['kebun'])) {
            $query->where('kebun', $this->filters['kebun']);
        }

        if (!empty($this->filters['divisi'])) {
            $query->where('divisi', $this->filters['divisi']);
        }

        if (!empty($this->filters['tahun'])) {
            $query->where('tahun', $this->filters['tahun']);
        }

        if (!empty($this->filters['bulan'])) {
            $query->where('bulan', $this->filters['bulan']);
        }

        return $query->orderBy('tanggal_panen', 'desc');
    }

    public function headings(): array
    {
        return [
            'Tanggal Panen',
            'Bulan',
            'Tahun',
            'Kebun',
            'Divisi',
            'AKP Panen (%)',
            'Jumlah TK Panen',
            'Luas Panen (Ha)',
            'JJG Panen (JJG)',
            'JJG Kirim (JJG)',
            'Ketrek',
            'Total JJG Kirim (JJG)',
            'Tonase Panen (Kg)',
            'Refraksi (Kg)',
            'Refraksi (%)',
            'Restant (JJG)',
            'BJR Hari Ini',
            'Output (Kg/HK)',
            'Output (Ha/HK)',
            'Budget Harian',
            'Timbang Kebun Harian',
            'Timbang PKS Harian',
            'Rotasi Panen',
            'Input By',
            'BJR Calculated',
            'AKP Calculated',
            'ACV Prod (%)',
            'Selisih (Kg)'
        ];
    }

    public function map($panenHarian): array
    {
        return [
            $panenHarian->tanggal_panen->format('d/m/Y'),
            $panenHarian->bulan,
            $panenHarian->tahun,
            $panenHarian->kebun,
            $panenHarian->divisi,
            $panenHarian->akp_panen,
            $panenHarian->jumlah_tk_panen,
            $panenHarian->luas_panen_ha,
            $panenHarian->jjg_panen_jjg,
            $panenHarian->jjg_kirim_jjg,
            $panenHarian->ketrek,
            $panenHarian->total_jjg_kirim_jjg,
            $panenHarian->tonase_panen_kg,
            $panenHarian->refraksi_kg,
            $panenHarian->refraksi_persen,
            $panenHarian->restant_jjg,
            $panenHarian->bjr_hari_ini,
            $panenHarian->output_kg_hk,
            $panenHarian->output_ha_hk,
            $panenHarian->budget_harian,
            $panenHarian->timbang_kebun_harian,
            $panenHarian->timbang_pks_harian,
            $panenHarian->rotasi_panen,
            $panenHarian->input_by,
            $panenHarian->bjr,
            $panenHarian->akp_calculated,
            $panenHarian->acv_prod,
            $panenHarian->selisih
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
