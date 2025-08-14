<?php

namespace App\Imports;

use App\Models\MasterData;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MasterDataImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Map bulan Indonesia ke English
        $bulanMap = [
            'januari' => 'January', 'februari' => 'February', 'maret' => 'March',
            'april' => 'April', 'mei' => 'May', 'juni' => 'June',
            'juli' => 'July', 'agustus' => 'August', 'september' => 'September',
            'oktober' => 'October', 'november' => 'November', 'desember' => 'December'
        ];

        $bulan = $row['bulan'];
        if (isset($bulanMap[strtolower($bulan)])) {
            $bulan = $bulanMap[strtolower($bulan)];
        }

        // Check if record already exists
        $exists = MasterData::where('kebun', $row['kebun'])
                           ->where('divisi', $row['divisi'])
                           ->where('tahun', $row['tahun'])
                           ->where('bulan', $bulan)
                           ->first();

        if ($exists) {
            // Update existing record
            $exists->update([
                'sph_panen' => $row['sph_panen'] ?? $exists->sph_panen,
                'luas_tm' => $row['luas_tm'] ?? $exists->luas_tm,
                'budget_alokasi' => $row['budget_alokasi'] ?? $exists->budget_alokasi,
                'pkk' => $row['pkk'] ?? $exists->pkk,
                'is_active' => isset($row['status']) ? 
                    (strtolower($row['status']) === 'aktif' ? true : false) : 
                    $exists->is_active
            ]);
            return null;
        }

        // Create new record
        return new MasterData([
            'kebun' => $row['kebun'],
            'divisi' => $row['divisi'],
            'sph_panen' => $row['sph_panen'] ?? 136,
            'luas_tm' => $row['luas_tm'] ?? 0,
            'budget_alokasi' => $row['budget_alokasi'] ?? 0,
            'pkk' => $row['pkk'] ?? 0,
            'bulan' => $bulan,
            'tahun' => $row['tahun'],
            'is_active' => isset($row['status']) ? 
                (strtolower($row['status']) === 'aktif' ? true : false) : 
                true
        ]);
    }

    public function rules(): array
    {
        return [
            'kebun' => 'required|string|max:64',
            'divisi' => 'required|string|max:64',
            'sph_panen' => 'nullable|numeric|min:0',
            'luas_tm' => 'nullable|numeric|min:0',
            'budget_alokasi' => 'nullable|numeric|min:0',
            'pkk' => 'nullable|integer|min:0',
            'bulan' => 'required|string',
            'tahun' => 'required|integer|min:2020|max:2050',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'kebun.required' => 'Kolom kebun harus diisi',
            'divisi.required' => 'Kolom divisi harus diisi',
            'bulan.required' => 'Kolom bulan harus diisi',
            'tahun.required' => 'Kolom tahun harus diisi',
        ];
    }
}
