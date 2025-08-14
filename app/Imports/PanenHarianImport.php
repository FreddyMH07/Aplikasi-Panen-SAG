<?php

namespace App\Imports;

use App\Models\PanenHarian;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class PanenHarianImport implements ToModel, WithHeadingRow, WithValidation
{
    public function headingRow(): int
    {
        return 1;
    }

    public function map($row): array
    {
        // Map header sesuai format baru
        return [
            'tanggal_panen' => $row[0] ?? null,
            'kebun' => $row[1] ?? null,
            'divisi' => $row[2] ?? null,
            'akp_panen' => $row[3] ?? null,
            'jumlah_tk_panen' => $row[4] ?? 0,
            'luas_panen_ha' => $row[5] ?? 0,
            'jjg_panen_jjg' => $row[6] ?? 0,
            'jjg_kirim_jjg' => $row[7] ?? 0,
            'ketrek' => $row[8] ?? null,
            'total_jjg_kirim_jjg' => $row[9] ?? 0,
            'tonase_panen_kg' => $row[10] ?? 0,
            'refraksi_kg' => $row[11] ?? 0,
            'refraksi_persen' => $row[12] ?? null,
            'restant_jjg' => $row[13] ?? 0,
            'bjr_hari_ini' => $row[14] ?? 0,
            'output_kg_hk' => $row[15] ?? 0,
            'output_ha_hk' => $row[16] ?? 0,
            'budget_harian' => $row[17] ?? 0,
            'timbang_kebun_harian' => $row[18] ?? 0,
            'timbang_pks_harian' => $row[19] ?? 0,
            'rotasi_panen' => $row[20] ?? 0,
            'bjr_calculated' => $row[21] ?? 0,
            'akp_calculated' => $row[22] ?? null,
            'acv_prod' => $row[23] ?? null,
            'selisih' => $row[24] ?? 0,
            'input_by' => $row[25] ?? 'Import',
        ];
    }

    public function model(array $row)
    {
        // Parse tanggal dengan format dd/mm/yyyy
        $tanggalPanen = null;
        if (!empty($row['tanggal_panen'])) {
            try {
                if (is_numeric($row['tanggal_panen'])) {
                    // Excel date format
                    $tanggalPanen = Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($row['tanggal_panen'] - 2);
                } else {
                    // String date format (dd/mm/yyyy)
                    $dateStr = $row['tanggal_panen'];
                    if (strpos($dateStr, '/') !== false) {
                        $tanggalPanen = Carbon::createFromFormat('d/m/Y', $dateStr);
                    } else {
                        $tanggalPanen = Carbon::parse($dateStr);
                    }
                }
            } catch (\Exception $e) {
                $tanggalPanen = Carbon::now();
            }
        }

        // Helper function untuk parse nilai dengan format yang beragam
        $parseNumeric = function($value, $default = null) {
            if (empty($value) || $value === '' || $value === null || $value === '-') {
                return $default;
            }
            
            // Remove formatting (commas, spaces)
            $cleanValue = str_replace([',', ' '], '', $value);
            
            return is_numeric($cleanValue) ? (float)$cleanValue : $default;
        };

        $parseInt = function($value, $default = null) {
            if (empty($value) || $value === '' || $value === null || $value === '-') {
                return $default;
            }
            
            // Remove formatting
            $cleanValue = str_replace([',', ' '], '', $value);
            
            return is_numeric($cleanValue) ? (int)$cleanValue : $default;
        };

        $parsePercentage = function($value) {
            if (empty($value) || $value === '' || $value === null) {
                return null;
            }
            
            // Remove % sign and convert to decimal
            $cleanValue = str_replace(['%', ' '], '', $value);
            return is_numeric($cleanValue) ? $cleanValue : null;
        };

        // Parse bulan dan tahun dari tanggal
        $bulan = $tanggalPanen ? $tanggalPanen->format('F') : null;
        $tahun = $tanggalPanen ? $tanggalPanen->year : null;

        return new PanenHarian([
            'tanggal_panen' => $tanggalPanen,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'kebun' => $row['kebun'] ?? '',
            'divisi' => $row['divisi'] ?? '',
            'akp_panen' => $parsePercentage($row['akp_panen']),
            'jumlah_tk_panen' => $parseInt($row['jumlah_tk_panen'], 0),
            'luas_panen_ha' => $parseNumeric($row['luas_panen_ha'], 0),
            'jjg_panen_jjg' => $parseInt($row['jjg_panen_jjg'], 0),
            'jjg_kirim_jjg' => $parseInt($row['jjg_kirim_jjg'], 0),
            'ketrek' => $parseNumeric($row['ketrek']),
            'total_jjg_kirim_jjg' => $parseInt($row['total_jjg_kirim_jjg'], 0),
            'tonase_panen_kg' => $parseNumeric($row['tonase_panen_kg'], 0),
            'refraksi_kg' => $parseNumeric($row['refraksi_kg'], 0),
            'refraksi_persen' => $parsePercentage($row['refraksi_persen']),
            'restant_jjg' => $parseInt($row['restant_jjg'], 0),
            'bjr_hari_ini' => $parseNumeric($row['bjr_hari_ini'], 0),
            'output_kg_hk' => $parseNumeric($row['output_kg_hk'], 0),
            'output_ha_hk' => $parseNumeric($row['output_ha_hk'], 0),
            'budget_harian' => $parseNumeric($row['budget_harian'], 0),
            'timbang_kebun_harian' => $parseNumeric($row['timbang_kebun_harian'], 0),
            'timbang_pks_harian' => $parseNumeric($row['timbang_pks_harian'], 0),
            'rotasi_panen' => $parseNumeric($row['rotasi_panen'], 0),
            'input_by' => $row['input_by'] ?? 'Import',
        ]);
    }

    public function rules(): array
    {
        return [
            'tanggal_panen' => 'required',
            'kebun' => 'required|string|max:64',
            'divisi' => 'required|string|max:64',
            'jumlah_tk_panen' => 'nullable|integer|min:0',
            'luas_panen_ha' => 'nullable|numeric|min:0',
            'jjg_panen_jjg' => 'nullable|integer|min:0',
            'jjg_kirim_jjg' => 'nullable|integer|min:0',
            'total_jjg_kirim_jjg' => 'nullable|integer|min:0',
            'tonase_panen_kg' => 'nullable|numeric|min:0',
            'refraksi_kg' => 'nullable|numeric|min:0',
            'refraksi_persen' => 'nullable|numeric|min:0',
            'restant_jjg' => 'nullable|integer|min:0',
            'bjr_hari_ini' => 'nullable|numeric|min:0',
            'output_kg_hk' => 'nullable|numeric|min:0',
            'output_ha_hk' => 'nullable|numeric|min:0',
            'budget_harian' => 'nullable|numeric|min:0',
            'timbang_kebun_harian' => 'nullable|numeric|min:0',
            'timbang_pks_harian' => 'nullable|numeric|min:0',
            'rotasi_panen' => 'nullable|numeric|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'tanggal_panen.required' => 'Kolom tanggal panen harus diisi',
            'kebun.required' => 'Kolom kebun harus diisi',
            'divisi.required' => 'Kolom divisi harus diisi',
        ];
    }
}
