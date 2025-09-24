<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetPanenDataCommand extends Command
{
    protected $signature = 'panen:reset-data {--confirm : Confirm destructive reset}';
    protected $description = 'Delete all data for Panen Harian and Panen Bulanan tables (TRUNCATE)';

    public function handle()
    {
        if (!$this->option('confirm')) {
            $this->error('This is destructive. Re-run with --confirm to proceed.');
            return self::FAILURE;
        }

        $this->warn('Truncating tables: panen_harians, panen_bulanans (if exists) ...');

        DB::beginTransaction();
        try {
            // Truncate daily first (monthly may depend on it)
            if ($this->tableExists('panen_harians')) {
                DB::statement('TRUNCATE TABLE panen_harians RESTART IDENTITY CASCADE');
                $this->info('Truncated panen_harians');
            } else {
                $this->line('Table panen_harians not found (skipped)');
            }

            // Monthly table may be materialized or normal table depending on app; truncate if present
            if ($this->tableExists('panen_bulanans')) {
                DB::statement('TRUNCATE TABLE panen_bulanans RESTART IDENTITY CASCADE');
                $this->info('Truncated panen_bulanans');
            } else {
                $this->line('Table panen_bulanans not found (skipped)');
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        // Show counts after
        $harians = $this->tableExists('panen_harians') ? (int) DB::table('panen_harians')->count() : 0;
        $bulanans = $this->tableExists('panen_bulanans') ? (int) DB::table('panen_bulanans')->count() : 0;
        $this->info("Rows now: panen_harians=$harians, panen_bulanans=$bulanans");
        return self::SUCCESS;
    }

    private function tableExists(string $table): bool
    {
        try {
            DB::select('select 1 from information_schema.tables where table_schema = current_schema() and table_name = ?', [$table]);
            // A select is cheap; use schema builder for clarity
            return DB::getSchemaBuilder()->hasTable($table);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
