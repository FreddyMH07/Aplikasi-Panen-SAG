<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Force HTTPS in production to respect proxy TLS termination
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
