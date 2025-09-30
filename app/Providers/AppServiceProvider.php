<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
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

        // Share safe user info to all views to avoid direct property access in Blade
        View::composer('*', function ($view) {
            $user = auth()->user();
            $view->with('userName', $user?->name ?? 'User');
            $view->with('userEmail', $user?->email ?? '');
        });
    }
}
