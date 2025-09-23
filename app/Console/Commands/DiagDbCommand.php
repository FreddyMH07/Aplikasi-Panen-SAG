<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\MasterData;
use App\Models\PanenHarian;

class DiagDbCommand extends Command
{
    protected $signature = 'app:diag-db {--sample=1 : Jumlah sample record per tabel}';
    protected $description = 'Diagnosa koneksi database aktif, ketersediaan tabel, jumlah data, dan anomali umum.';

    public function handle(): int
    {
        $this->info('=== Database Diagnostic ===');

        $default = config('database.default');
        $conn = DB::connection();
        $driver = $conn->getDriverName();
        $config = config("database.connections.$default");

        $this->line('Driver    : ' . $driver);
        $this->line('Connection: ' . $default);
        $this->line('Host      : ' . ($config['host'] ?? '-'));
        $this->line('Port      : ' . ($config['port'] ?? '-'));
        $this->line('Database  : ' . ($config['database'] ?? '-'));
        $this->line('Schema    : ' . ($config['search_path'] ?? '-'));

        // Check key tables
        $tables = [
            'master_data', 'panen_harians', 'panen_harians_old', 'kebuns', 'divisis'
        ];

        $exists = [];
        foreach ($tables as $t) {
            $exists[$t] = Schema::hasTable($t);
        }

        $this->info('\nTabel:');
        foreach ($exists as $t => $ok) {
            $this->line(sprintf('- %-20s %s', $t, $ok ? 'ADA' : 'TIDAK ADA'));
        }

        // Counts
        $counts = [];
        foreach (['master_data','panen_harians','panen_harians_old'] as $t) {
            if ($exists[$t]) {
                try { $counts[$t] = DB::table($t)->count(); } catch (\Throwable $e) { $counts[$t] = 'ERR'; }
            }
        }

        $this->info('\nJumlah Record:');
        foreach ($counts as $t => $c) {
            $this->line(sprintf('- %-20s %s', $t, $c));
        }

        // Sample records
        $sampleSize = (int)$this->option('sample');
        if ($sampleSize > 0) {
            $this->info("\nSample (max $sampleSize per tabel):");
            if ($exists['master_data']) {
                $sample = MasterData::limit($sampleSize)->get(['id','kebun','divisi','bulan','tahun']);
                $this->line('master_data: ' . $sample->toJson());
            }
            if ($exists['panen_harians']) {
                $sample = PanenHarian::limit($sampleSize)->get(['id','tanggal_panen','kebun','divisi','bulan','tahun']);
                $this->line('panen_harians: ' . $sample->toJson());
            }
            if ($exists['panen_harians_old']) {
                $sample = DB::table('panen_harians_old')->limit($sampleSize)->get(['id']);
                $this->line('panen_harians_old: ' . $sample->toJson());
            }
        }

        // Check potential mismatch: data ada di old tapi kosong di new
        if (($counts['panen_harians_old'] ?? 0) > 0 && (($counts['panen_harians'] ?? 0) == 0)) {
            $this->warn('\nPERINGATAN: Data masih ada di panen_harians_old tetapi panen_harians baru kosong. Pertimbangkan migrasi data.');
        }

        // Check month naming mismatch
        if ($exists['master_data']) {
            $distinctMonths = DB::table('master_data')->select('bulan')->distinct()->pluck('bulan')->toArray();
            $invalid = array_filter($distinctMonths, function($m) {
                return !in_array($m, ['January','February','March','April','May','June','July','August','September','October','November','December']);
            });
            if ($invalid) {
                $this->warn('\nPERINGATAN: Nilai kolom bulan tidak sesuai English month: ' . implode(', ', $invalid));
            }
        }

        $this->info('\nSelesai.');
        return self::SUCCESS;
    }
}
