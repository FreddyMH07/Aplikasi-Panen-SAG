<?php

namespace App\Imports;

use App\Models\PanenHarian;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class PanenHarianImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * Normalize various date formats commonly found in CSV/Excel exports.
     * Accepts numeric Excel serials, M/D/Y and D/M/Y with '/' or '-', and ISO-like forms.
     */
    private function parseTanggalPanen($value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        // If numeric, treat as Excel serial date (1900 system, account for leap-year bug)
        if (is_numeric($value)) {
            try {
                $serial = (int)$value;
                // Guard: ignore obviously invalid serials
                if ($serial > 0 && $serial < 60000) { // ~ 2064-11-20 upper bound
                    return Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($serial - 2);
                }
            } catch (\Exception $e) {
                // fall through to other strategies
            }
        }

        // Coerce to string and trim
        $dateStr = trim((string)$value);

        // Replace '.' with '/' if any, to unify delimiters
        $dateStr = str_replace('.', '/', $dateStr);

        // Try common explicit formats. Prefer M/D/Y first (as seen in provided CSV),
        // then D/M/Y for Indonesian-style dates. Support single-digit month/day.
        $formats = [
            'n/j/Y', 'm/d/Y', // M/D/Y
            'j/n/Y', 'd/m/Y', // D/M/Y
            'Y-m-d', 'Y/m/d', 'd-m-Y', 'm-d-Y', // other common
        ];

        foreach ($formats as $fmt) {
            try {
                $dt = Carbon::createFromFormat($fmt, $dateStr);
                // Ensure no trailing characters by re-formatting back
                if ($dt !== false) {
                    return $dt;
                }
            } catch (\Exception $e) {
                // try next format
            }
        }

        // Last resort: Carbon::parse (handles some locale/ISO variants)
        try {
            $dt = Carbon::parse($dateStr);
            return $dt;
        } catch (\Exception $e) {
            return null;
        }
    }

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
        // Robust parse for tanggal (accept M/D/Y and D/M/Y and Excel serials)
        $tanggalPanen = $this->parseTanggalPanen($row['tanggal_panen'] ?? null);

        // Guard against out-of-range years (e.g., mis-parsed 2026/2027 due to format swap).
        // If year is implausible (> current year + 1 or < 2000), attempt swapping interpretation once more.
        if ($tanggalPanen) {
            $year = (int)$tanggalPanen->year;
            $currentYear = (int) now()->year;
            if ($year > $currentYear + 1 || $year < 2000) {
                // Try the alternate day/month interpretation if original was slash-delimited
                $raw = $row['tanggal_panen'] ?? '';
                if (is_string($raw) && strpos($raw, '/') !== false) {
                    // Swap by trying the opposite primary formats
                    $altFormats = [
                        'j/n/Y', 'd/m/Y', 'n/j/Y', 'm/d/Y'
                    ];
                    $altParsed = null;
                    foreach ($altFormats as $fmt) {
                        try {
                            $altParsed = Carbon::createFromFormat($fmt, trim($raw));
                            if ($altParsed) break;
                        } catch (\Exception $e) {}
                    }
                    if ($altParsed) {
                        $altYear = (int)$altParsed->year;
                        if (!($altYear > $currentYear + 1 || $altYear < 2000)) {
                            $tanggalPanen = $altParsed;
                        }
                    }
                }
            }
        }

        // If still not parsable, skip this row to avoid corrupt dates
        if (!$tanggalPanen) {
            return null;
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

        $parsePercentage = function($value, $default = 0) {
            if ($value === '' || $value === null || $value === '-') {
                return $default;
            }
            // Remove % sign and spaces
            $cleanValue = str_replace(['%', ' '], '', (string)$value);
            return is_numeric($cleanValue) ? (float)$cleanValue : $default;
        };

        // Parse bulan dan tahun dari tanggal
        $bulan = $tanggalPanen ? $tanggalPanen->format('F') : null;
        $tahun = $tanggalPanen ? $tanggalPanen->year : null;

        // Normalize kebun/divisi to reduce duplicates due to casing/spacing
        $normalizeStr = function($v) {
            if ($v === null) return '';
            $s = trim((string)$v);
            // Collapse multiple spaces to single
            $s = preg_replace('/\s+/', ' ', $s);
            return strtoupper($s);
        };

        $kebun = $normalizeStr($row['kebun'] ?? '');
        $divisi = $normalizeStr($row['divisi'] ?? '');

        $attributes = [
            'tanggal_panen' => $tanggalPanen,
            'kebun' => $kebun,
            'divisi' => $divisi,
        ];

        $values = [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'akp_panen' => $parsePercentage($row['akp_panen']),
            'jumlah_tk_panen' => $parseInt($row['jumlah_tk_panen'], 0),
            'luas_panen_ha' => $parseNumeric($row['luas_panen_ha'], 0),
            'jjg_panen_jjg' => $parseInt($row['jjg_panen_jjg'], 0),
            'jjg_kirim_jjg' => $parseInt($row['jjg_kirim_jjg'], 0),
            'ketrek' => $parseNumeric($row['ketrek']),
            'total_jjg_kirim_jjg' => $parseInt($row['total_jjg_kirim_jjg'], 0),
            'tonase_panen_kg' => $parseNumeric($row['tonase_panen_kg'], 0),
            'refraksi_kg' => $parseNumeric($row['refraksi_kg'], 0),
            'refraksi_persen' => $parsePercentage($row['refraksi_persen'], 0),
            'restant_jjg' => $parseInt($row['restant_jjg'], 0),
            'bjr_hari_ini' => $parseNumeric($row['bjr_hari_ini'], 0),
            'output_kg_hk' => $parseNumeric($row['output_kg_hk'], 0),
            'output_ha_hk' => $parseNumeric($row['output_ha_hk'], 0),
            'budget_harian' => $parseNumeric($row['budget_harian'], 0),
            'timbang_kebun_harian' => $parseNumeric($row['timbang_kebun_harian'], 0),
            'timbang_pks_harian' => $parseNumeric($row['timbang_pks_harian'], 0),
            'rotasi_panen' => $parseNumeric($row['rotasi_panen'], 0),
            'input_by' => $row['input_by'] ?? 'Import',
        ];

        // Upsert by (tanggal_panen, kebun, divisi)
        PanenHarian::updateOrCreate($attributes, $values);
        return null;
    }

    public function rules(): array
    {
        return [
            'tanggal_panen' => 'required',
            'kebun' => 'required|string|max:64',
            'divisi' => 'required|string|max:64',
            'akp_panen' => 'nullable|numeric|min:0|max:100',
            'jumlah_tk_panen' => 'nullable|numeric|min:0',
            'luas_panen_ha' => 'nullable|numeric|min:0',
            'jjg_panen_jjg' => 'nullable|numeric|min:0',
            'jjg_kirim_jjg' => 'nullable|numeric|min:0',
            'total_jjg_kirim_jjg' => 'nullable|numeric|min:0',
            'tonase_panen_kg' => 'nullable|numeric|min:0',
            'refraksi_kg' => 'nullable|numeric|min:0',
            'refraksi_persen' => 'nullable|numeric|min:0',
            'restant_jjg' => 'nullable|numeric|min:0',
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
