<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DbDiagCommand extends Command
{
    protected $signature = 'app:db-diag {--timeout=3 : Timeout detik per query uji}';
    protected $description = 'Diagnostik koneksi PostgreSQL: DNS, TCP, versi server, schema, hitung tabel & migrations.';

    public function handle(): int
    {
        $this->line('<info>[db-diag] Driver:</info> '.config('database.default'));
        $url = env('DATABASE_URL');
        if ($url) $this->line('<info>[db-diag] DATABASE_URL:</info> '.$url);
        foreach (['DB_HOST','DB_PORT','DB_DATABASE','DB_USERNAME','PGSSLMODE'] as $v) {
            $val = env($v);
            if ($val !== null) $this->line("[db-diag] $v=$val");
        }

        // Low-level socket test if possible
        $host = env('DB_HOST');
        $port = env('DB_PORT','5432');
        if ($host) {
            $this->line('[db-diag] Trying fsockopen to host:port ...');
            $start = microtime(true);
            $errNo = 0; $errStr = '';
            $conn = @fsockopen($host, (int)$port, $errNo, $errStr, 2.5);
            $elapsed = number_format((microtime(true)-$start)*1000,1);
            if ($conn) { $this->line("<info>[db-diag] TCP OK</info> (${elapsed}ms)"); fclose($conn);} else { $this->error("[db-diag] TCP FAIL ($errNo) $errStr (${elapsed}ms)"); }
        }

        try {
            $version = DB::select('select version() as v');
            $this->info('[db-diag] Connected. Server version: '.($version[0]->v ?? 'n/a'));
        } catch (\Throwable $e) {
            $this->error('[db-diag] Connection FAILED: '.substr($e->getMessage(),0,180));
            return self::FAILURE;
        }

        try {
            $tables = DB::select("select table_name from information_schema.tables where table_schema=current_schema() order by 1");
            $this->line('[db-diag] Tables ('.count($tables).'): '.implode(',', array_map(fn($r)=>$r->table_name,$tables)));
        } catch (\Throwable $e) {
            $this->warn('[db-diag] List tables error: '.substr($e->getMessage(),0,140));
        }

        try {
            $migrated = DB::select("select count(*) c from migrations");
            $this->line('[db-diag] migrations rows='.$migrated[0]->c);
        } catch (\Throwable $e) {
            $this->warn('[db-diag] migrations table missing or error: '.substr($e->getMessage(),0,140));
        }

        return self::SUCCESS;
    }
}
