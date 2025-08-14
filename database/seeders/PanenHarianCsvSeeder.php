<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PanenHarian;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PanenHarianCsvSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = base_path('Template Import Panen Harian PT SAG.csv');
        if (!file_exists($file)) {
            $this->command?->warn("Panen harian CSV not found: $file");
            return;
        }
        $handle = fopen($file, 'r');
        if (!$handle) {
            $this->command?->error('Cannot open panen harian CSV.');
            return;
        }
        $header = null;
        $inserted = 0; $skipped = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if ($header === null) {
                $header = array_map(fn($h) => strtolower(trim($h)), $row);
                continue;
            }
            if (count(array_filter($row, fn($v) => $v !== null && $v !== '')) === 0) {
                continue; // empty line
            }
            $data = [];
            foreach ($row as $i => $value) {
                $key = $header[$i] ?? 'col_'.$i;
                $data[$key] = trim($value);
            }

            if (!isset($data['tanggal_panen'], $data['kebun'], $data['divisi'])) {
                continue;
            }

            // Parse date (allow n/j/Y or Y-m-d)
            $tanggalStr = $data['tanggal_panen'];
            $tanggal = null;
            $formats = ['n/j/Y', 'Y-m-d', 'd/m/Y'];
            foreach ($formats as $fmt) {
                try {
                    $tanggal = Carbon::createFromFormat($fmt, $tanggalStr);
                    break;
                } catch (\Exception $e) { /* continue */ }
            }
            if (!$tanggal) {
                try { $tanggal = Carbon::parse($tanggalStr); } catch (\Exception $e) { continue; }
            }

            $bulan = $tanggal->format('F');
            $tahun = $tanggal->year;

            $exists = PanenHarian::whereDate('tanggal_panen', $tanggal->toDateString())
                ->where('kebun', $data['kebun'])
                ->where('divisi', $data['divisi'])
                ->first();
            if ($exists) { $skipped++; continue; }

            // Additional data (columns not part of fillable)
            $extraKeys = ['bjr_calc','akp_calc','acv0_prod','selisih'];
            $additional = [];
            foreach ($extraKeys as $k) {
                if (isset($data[$k])) { $additional[$k] = is_numeric($data[$k]) ? (float)$data[$k] : $data[$k]; }
            }

            PanenHarian::create([
                'tanggal_panen' => $tanggal->toDateString(),
                'bulan' => $bulan,
                'tahun' => $tahun,
                'kebun' => $data['kebun'],
                'divisi' => $data['divisi'],
                'akp_panen' => $data['akp_panen'] !== '' ? (float)$data['akp_panen'] : null,
                'jumlah_tk_panen' => (int)($data['jumlah_tk_panen'] ?? 0),
                'luas_panen_ha' => (float)($data['luas_panen_ha'] ?? 0),
                'jjg_panen_jjg' => (int)($data['jjg_panen_jjg'] ?? 0),
                'jjg_kirim_jjg' => (int)($data['jjg_kirim_jjg'] ?? 0),
                'ketrek' => $data['ketrek'] !== '' ? (float)$data['ketrek'] : null,
                'total_jjg_kirim_jjg' => (int)($data['total_jjg_kirim_jjg'] ?? 0),
                'tonase_panen_kg' => (float)($data['tonase_panen_kg'] ?? 0),
                'refraksi_kg' => (float)($data['refraksi_kg'] ?? 0),
                'refraksi_persen' => (float)($data['refraksi_persen'] ?? 0),
                'restant_jjg' => (int)($data['restant_jjg'] ?? 0),
                'bjr_hari_ini' => (float)($data['bjr_hari_ini'] ?? 0),
                'output_kg_hk' => (float)($data['output_kg_hk'] ?? 0),
                'output_ha_hk' => (float)($data['output_ha_hk'] ?? 0),
                'budget_harian' => (float)($data['budget_harian'] ?? 0),
                'timbang_kebun_harian' => (float)($data['timbang_kebun_harian'] ?? 0),
                'timbang_pks_harian' => (float)($data['timbang_pks_harian'] ?? 0),
                'rotasi_panen' => (float)($data['rotasi_panen'] ?? 0),
                'input_by' => 'Seeder CSV',
                'additional_data' => $additional ?: null,
            ]);
            $inserted++;
        }
        fclose($handle);
        $this->command?->info("PanenHarianCsvSeeder: inserted $inserted, skipped $skipped (already existed).");
    }
}
