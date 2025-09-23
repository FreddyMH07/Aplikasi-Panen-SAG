<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DbSnapshotCommand extends Command
{
    protected $signature = 'app:db-snapshot
        {--table= : Hanya snapshot untuk satu tabel tertentu}
        {--limit=5 : Jumlah sample row per tabel}
        {--raw : Tampilkan JSON mentah tanpa format tambahan}';

    protected $description = 'Snapshot cepat seluruh database: daftar tabel, jumlah record, dan sample isi.';

    public function handle(): int
    {
        $driver = DB::getDriverName();
        $this->info("=== DB Snapshot ($driver) ===");

        $targetTable = $this->option('table');
        $limit = (int) $this->option('limit');
        if ($limit < 1) { $limit = 5; }

        $tables = $this->getTableList($driver);
        if (!$tables) {
            $this->warn('Tidak ada tabel ditemukan.');
            return self::SUCCESS;
        }

        if ($targetTable) {
            if (!in_array($targetTable, $tables, true)) {
                $this->error("Tabel '$targetTable' tidak ditemukan.");
                return self::FAILURE;
            }
            $tables = [$targetTable];
        }

        $rowsOutput = [];
        foreach ($tables as $t) {
            try {
                $count = DB::table($t)->count();
            } catch (\Throwable $e) {
                $count = 'ERR:' . $e->getCode();
            }

            $sample = [];
            if (is_numeric($count) && $count > 0) {
                try {
                    // Hindari ambil semua kolom pada tabel sangat lebar dengan limit.
                    $sample = DB::table($t)->limit($limit)->get();
                } catch (\Throwable $e) {
                    $sample = ['error' => $e->getMessage()];
                }
            }

            $rowsOutput[] = [
                'table' => $t,
                'count' => $count,
                'sample' => $sample,
            ];
        }

        if ($this->option('raw')) {
            $this->line(json_encode($rowsOutput, JSON_PRETTY_PRINT));
        } else {
            foreach ($rowsOutput as $row) {
                $this->line(str_repeat('-', 60));
                $this->info(sprintf('%s (%s)', $row['table'], $row['count']));
                if (is_array($row['sample']) && isset($row['sample']['error'])) {
                    $this->error('Sample error: ' . $row['sample']['error']);
                } elseif (count($row['sample'])) {
                    $this->line(substr(json_encode($row['sample'], JSON_UNESCAPED_UNICODE), 0, 4000));
                } else {
                    $this->line('[no rows]');
                }
            }
            $this->line(str_repeat('-', 60));
        }

        $this->info('Selesai.');
        return self::SUCCESS;
    }

    private function getTableList(string $driver): array
    {
        try {
            if ($driver === 'pgsql') {
                $res = DB::select("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname = current_schema() ORDER BY tablename");
                $tables = array_map(fn($o) => $o->tablename, $res);
            } elseif ($driver === 'mysql') {
                $db = DB::getDatabaseName();
                $res = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
                $key = 'Tables_in_' . $db;
                $tables = array_map(fn($o) => $o->$key, $res);
            } else { // sqlite & fallback
                $res = DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
                $tables = array_map(fn($o) => $o->name, $res);
            }
        } catch (\Throwable $e) {
            $this->error('Gagal mengambil daftar tabel: ' . $e->getMessage());
            return [];
        }

        // Filter tabel internal yang tidak perlu biasanya
        $ignore = ['migrations', 'password_reset_tokens', 'failed_jobs', 'cache', 'job_batches', 'sessions'];
        return array_values(array_diff($tables, $ignore));
    }
}
