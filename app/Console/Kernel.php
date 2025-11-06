<?php

namespace App\Console;

use \Illuminate\Support\Facades\Log;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('create:mining-daily-stat')->dailyAt('00:01');
        $schedule->command('generate:mining-reward')->dailyAt('00:06');
        $schedule->command('reflect:asset-deposit')->dailyAt('00:11');
        $schedule->command('grant:rank-bonus')->dailyAt('00:16');

        $schedule->command('uploads:clean-tmp')->hourly();
        $schedule->command('crypto:fetch-prices')->everyMinute();
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
