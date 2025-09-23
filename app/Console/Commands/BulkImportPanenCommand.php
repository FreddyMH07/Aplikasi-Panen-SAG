<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MasterDataImport;
use App\Imports\PanenHarianImport;
use Illuminate\Support\Facades\Storage;
use Throwable;

class BulkImportPanenCommand extends Command
{
    protected $signature = 'panen:bulk-import 
        {--master= : Path ke file Master Data (xlsx/csv)} 
        {--harian= : Path ke file Panen Harian (xlsx/csv)} 
        {--disk=local : Disk storage Laravel bila path relatif} 
        {--dry-run : Validasi tanpa simpan (hanya hitung baris)}';

    protected $description = 'Bulk import Master Data dan Panen Harian secara otomatis';

    public function handle(): int
    {
        $masterPath = $this->option('master');
        $harianPath = $this->option('harian');
        $disk = $this->option('disk');
        $dry = (bool)$this->option('dry-run');

        if (!$masterPath && !$harianPath) {
            $this->error('Minimal salah satu --master atau --harian harus diisi.');
            return self::FAILURE;
        }

        $summary = [];

        if ($masterPath) {
            $summary['master'] = $this->importFile($masterPath, new MasterDataImport, $disk, $dry, 'master_data');
        }
        if ($harianPath) {
            $summary['panen_harian'] = $this->importFile($harianPath, new PanenHarianImport, $disk, $dry, 'panen_harians');
        }

        $this->table(['Dataset','File','Rows Imported','Notes'], collect($summary)->map(function($s,$k){
            return [
                $k,
                $s['file'] ?? '-',
                $s['imported'] ?? 0,
                $s['notes'] ?? ''
            ];
        })->toArray());

        if ($dry) {
            $this->comment('Dry run selesai. Tidak ada data yang disimpan.');
        }
        $this->info('Selesai.');
        return self::SUCCESS;
    }

    protected function importFile(string $path, $importInstance, string $disk, bool $dry, string $table): array
    {
        $isAbsolute = str_starts_with($path, DIRECTORY_SEPARATOR) || preg_match('/^[A-Za-z]:\\\\/',$path);
        $resolved = $isAbsolute ? $path : Storage::disk($disk)->path($path);
        if (!file_exists($resolved)) {
            return ['file'=>$resolved,'imported'=>0,'notes'=>'File tidak ditemukan'];
        }

        $rowCount = null;
        if (preg_match('/\.csv$/i', $resolved)) {
            $rowCount = max(0, count(file($resolved)) - 1);
        }

        if ($dry) {
            return [
                'file' => $resolved,
                'imported' => 0,
                'notes' => 'Dry run. Est rows: '.($rowCount ?? 'unknown')
            ];
        }

        $before = \DB::table($table)->count();
        try {
            Excel::import($importInstance, $resolved);
        } catch (Throwable $e) {
            return [
                'file'=>$resolved,
                'imported'=>0,
                'notes'=>'Error: '.$e->getMessage()
            ];
        }
        $after = \DB::table($table)->count();
        return [
            'file'=>$resolved,
            'imported'=> $after - $before,
            'notes'=>'OK'
        ];
    }
}
