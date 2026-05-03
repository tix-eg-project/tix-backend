<?php


namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\VSoftSyncCities::class,
        \App\Console\Commands\VSoftSyncProducts::class,
    ];

    protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule): void
    {
        $schedule->command('vsoft:sync-cities')->dailyAt('03:00');
    }


    // protected function commands(): void
    // {
    //     $this->load(__DIR__ . '/Commands');

    //     require base_path('routes/console.php');
    // }
}
