<?php

namespace App\Exports;

use App\Models\PanenHarian;
use App\Models\MasterData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PanenBulananExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = PanenHarian::selectRaw('
            tahun,
            bulan,
            kebun,
            divisi,
            SUM(luas_panen_ha) as total_luas_panen,
            SUM(jjg_panen_jjg) as total_jjg_panen,
            SUM(timbang_kebun_harian) as total_timbang_kebun,
            SUM(timbang_pks_harian) as total_timbang_pks,
            SUM(jumlah_tk_panen) as total_jumlah_tk,
            SUM(refraksi_kg) as total_refraksi_kg,
            SUM(budget_harian) as total_budget,
            SUM(tonase_panen_kg) as total_tonase,
            AVG(CASE WHEN jjg_panen_jjg > 0 THEN timbang_kebun_harian / jjg_panen_jjg ELSE 0 END) as bjr_bulanan,
            COUNT(*) as hari_panen
        ')
        ->groupBy('tahun', 'bulan', 'kebun', 'divisi');

        // Apply filters
        if (!empty($this->filters['tahun'])) {
            $query->where('tahun', $this->filters['tahun']);
        }

        if (!empty($this->filters['bulan'])) {
            $query->where('bulan', $this->filters['bulan']);
        }

        if (!empty($this->filters['kebun'])) {
            $query->where('kebun', $this->filters['kebun']);
        }

        if (!empty($this->filters['divisi'])) {
            $query->where('divisi', $this->filters['divisi']);
        }

        return $query->orderBy('tahun', 'desc')
                     ->orderBy('bulan')
                     ->orderBy('kebun')
                     ->orderBy('divisi')
                     ->get();
    }

    public function headings(): array
    {
        return [
            'Tahun',
            'Bulan',
            'Kebun',
            'Divisi',
            'Total Luas Panen (Ha)',
            'Total JJG Panen',
            'Total Timbang Kebun (Kg)',
            'Total Timbang PKS (Kg)',
            'Total HK',
            'Total Refraksi (Kg)',
            'Total Budget',
            'Total Tonase (Kg)',
            'BJR Bulanan (Kg)',
            'AKP Bulanan',
            'ACV Prod Bulanan (%)',
            'Selisih (Kg)',
            'Refraksi (%)',
            'Hari Panen'
        ];
    }

    public function map($row): array
    {
        // Get SPH from master data
        $masterData = MasterData::getByKebunDivisi($row->kebun, $row->divisi, $row->tahun, $row->bulan);
        $sph = $masterData ? $masterData->sph_panen : 136;
        
        // Calculate metrics
        $akp = ($row->total_luas_panen * $sph) > 0 ? 
            ($row->total_jjg_panen / ($row->total_luas_panen * $sph)) : 0;
        
        $acvProd = $row->total_budget > 0 ? 
            (($row->total_timbang_pks / $row->total_budget) * 100) : 0;
        
        $selisih = $row->total_timbang_pks - $row->total_timbang_kebun;
        
        $refraksiPersen = $row->total_tonase > 0 ? 
            (($row->total_refraksi_kg / $row->total_tonase) * 100) : 0;

        // Map bulan to Indonesian
        $bulanMap = [
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
            'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
        ];

        return [
            $row->tahun,
            $bulanMap[$row->bulan] ?? $row->bulan,
            $row->kebun,
            $row->divisi,
            $row->total_luas_panen,
            $row->total_jjg_panen,
            $row->total_timbang_kebun,
            $row->total_timbang_pks,
            $row->total_jumlah_tk,
            $row->total_refraksi_kg,
            $row->total_budget,
            $row->total_tonase,
            $row->bjr_bulanan,
            $akp,
            $acvProd,
            $selisih,
            $refraksiPersen,
            $row->hari_panen
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
