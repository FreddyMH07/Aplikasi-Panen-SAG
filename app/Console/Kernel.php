<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     * (Optional explicit registration; directory auto-load tetap berjalan.)
     */
    protected $commands = [
        \App\Console\Commands\DiagDbCommand::class,
        \App\Console\Commands\DbSnapshotCommand::class,
        \App\Console\Commands\BulkImportPanenCommand::class,
        \App\Console\Commands\PanenDiagCommand::class,
    ];
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
