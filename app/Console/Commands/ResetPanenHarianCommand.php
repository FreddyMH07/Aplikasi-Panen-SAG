<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PanenHarianImport;

class ResetPanenHarianCommand extends Command
{
    protected $signature = 'panen:reset-harian {file : Path to CSV/XLSX file (absolute or relative to storage/app)} {--confirm : Confirm destructive reset}';
    protected $description = 'TRUNCATE panen_harians and re-import from a given file';

    public function handle()
    {
        $file = $this->argument('file');
        $confirm = (bool)$this->option('confirm');

        if (!$confirm) {
            $this->error('Refusing to reset without --confirm flag.');
            return self::FAILURE;
        }

        // Resolve path without hitting Storage (avoid fileinfo requirement here)
        if ($this->isAbsolutePath($file)) {
            $filePath = $file;
        } else {
            $filePath = base_path('storage'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.$file);
        }

        if (!file_exists($filePath)) {
            $this->error('File not found: ' . $filePath);
            return self::FAILURE;
        }

    $this->warn('About to TRUNCATE table panen_harians and re-import from: ' . $filePath);

        DB::beginTransaction();
        try {
            DB::statement('TRUNCATE TABLE panen_harians RESTART IDENTITY CASCADE');
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Failed to truncate: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info('Importing...');
        try {
            Excel::import(new PanenHarianImport, $filePath);
        } catch (\Throwable $e) {
            $this->error('Import failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        $count = DB::table('panen_harians')->count();
        $this->info('Done. Rows in panen_harians: ' . $count);
        return self::SUCCESS;
    }

    private function isAbsolutePath(string $path): bool
    {
        // Windows (C:\ or \\UNC) or Unix (/...)
        return (bool)preg_match('~^([a-zA-Z]:\\\\|/|\\\\\\\\)~', $path);
    }
}
