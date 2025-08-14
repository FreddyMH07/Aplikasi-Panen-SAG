<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register console command dynamically if running in console
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\SeedCsvCommand::class,
                \App\Console\Commands\BackupSqliteCommand::class,
            ]);
        }
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
