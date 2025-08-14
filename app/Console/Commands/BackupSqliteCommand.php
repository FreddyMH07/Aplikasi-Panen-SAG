<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BackupSqliteCommand extends Command
{
    protected $signature = 'app:backup-sqlite {--keep=10 : Keep only newest N backups} {--no-compress : Do not create zip archive}';
    protected $description = 'Create a timestamped backup copy (and optional zip) of the current SQLite database.';

    public function handle(): int
    {
        $path = config('database.connections.sqlite.database');
        if (!$path || !file_exists($path)) {
            $this->error('SQLite database file not found: '.$path);
            return self::FAILURE;
        }

        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = now()->format('Ymd-His');
        $baseName = 'database-'.$timestamp.'.sqlite';
        $target = $backupDir.DIRECTORY_SEPARATOR.$baseName;

        if (!copy($path, $target)) {
            $this->error('Failed to copy database to backup folder.');
            return self::FAILURE;
        }
        $this->info('Raw copy created: '.basename($target));

        if (!$this->option('no-compress')) {
            $zipName = $baseName.'.zip';
            $zipPath = $backupDir.DIRECTORY_SEPARATOR.$zipName;
            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE) === true) {
                $zip->addFile($target, $baseName);
                $zip->close();
                $this->info('Zip archive created: '.basename($zipPath));
            } else {
                $this->warn('Could not create zip archive.');
            }
        }

        $keep = (int)$this->option('keep');
        $files = collect(File::files($backupDir))
            ->filter(fn($f) => Str::contains($f->getFilename(), 'database-') && $f->getExtension() === 'sqlite')
            ->sortByDesc(fn($f) => $f->getMTime())
            ->values();
        if ($files->count() > $keep) {
            $toDelete = $files->slice($keep);
            foreach ($toDelete as $f) {
                @unlink($f->getPathname());
                $zipCandidate = $f->getPathname().'.zip';
                if (file_exists($zipCandidate)) @unlink($zipCandidate);
            }
            $this->info('Pruned '.count($toDelete).' old backup(s).');
        }

        $this->info('Backup complete.');
        return self::SUCCESS;
    }
}
