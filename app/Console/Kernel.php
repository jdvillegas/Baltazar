<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Ejecutar el job de limpieza de casos anulados cada día a las 3:00 AM
        $schedule->job(new \App\Jobs\CleanAnulledCases())->dailyAt('3:00');
        
        // Sincronizar actuaciones cada 6 horas
        $schedule->command('actuaciones:schedule-sync')
                 ->everySixHours()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->appendOutputTo(storage_path('logs/actuaciones-sync.log'));
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
