<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterData;
use Illuminate\Support\Facades\Log;

class MasterDataCsvSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = base_path('Template Import Master Data Panen PT SAG.csv');
        if (!file_exists($file)) {
            $this->command?->warn("Master data CSV not found: $file");
            return;
        }

        $handle = fopen($file, 'r');
        if (!$handle) {
            $this->command?->error('Cannot open master data CSV.');
            return;
        }

        $header = null;
        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if ($header === null) {
                $header = array_map(fn($h) => strtolower(trim($h)), $row);
                continue;
            }
            if (count(array_filter($row, fn($v) => $v !== null && $v !== '')) === 0) {
                continue; // skip empty line
            }
            $data = [];
            foreach ($row as $i => $value) {
                $key = $header[$i] ?? 'col_'.$i;
                $data[$key] = trim($value);
            }

            // Basic validation
            if (!isset($data['kebun'], $data['divisi'], $data['bulan'], $data['tahun'])) {
                continue;
            }

            // Normalize bulan (already English per file)
            $bulan = $data['bulan'];

            $existing = MasterData::where('kebun', $data['kebun'])
                ->where('divisi', $data['divisi'])
                ->where('bulan', $bulan)
                ->where('tahun', (int)$data['tahun'])
                ->first();

            $payload = [
                'kebun' => $data['kebun'],
                'divisi' => $data['divisi'],
                'sph_panen' => (float)($data['sph_panen'] ?? 0),
                'luas_tm' => (float)($data['luas_tm'] ?? 0),
                'budget_alokasi' => (float)($data['budget_alokasi'] ?? 0),
                'pkk' => (int)($data['pkk'] ?? 0),
                'bulan' => $bulan,
                'tahun' => (int)$data['tahun'],
                'is_active' => true,
            ];

            if ($existing) {
                $existing->update($payload);
            } else {
                MasterData::create($payload);
                $count++;
            }
        }
        fclose($handle);
        $this->command?->info("MasterDataCsvSeeder: inserted $count records (others updated/skipped).");
    }
}
