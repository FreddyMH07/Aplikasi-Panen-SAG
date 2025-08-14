<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedCsvCommand extends Command
{
    protected $signature = 'app:seed-csv {--fresh : Migrate fresh before seeding}';
    protected $description = 'Run CSV-based seeders (MasterDataCsvSeeder & PanenHarianCsvSeeder) optionally with fresh migration';

    public function handle(): int
    {
        if ($this->option('fresh')) {
            $this->call('migrate:fresh', ['--force' => true]);
        }
        $this->call('db:seed', ['--class' => 'Database\\Seeders\\MasterDataCsvSeeder', '--force' => true]);
        $this->call('db:seed', ['--class' => 'Database\\Seeders\\PanenHarianCsvSeeder', '--force' => true]);
        $this->info('CSV seeders executed successfully.');
        return self::SUCCESS;
    }
}
