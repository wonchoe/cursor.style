<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
    
    protected $commands = [
        Commands\DailyQuote::class,
        Commands\randomTop::class,
        Commands\GetYandexMetrika::class,
        Commands\getExtensionData::class    
        //
    ];
    

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        $schedule->command('quote:daily')->daily();
        $schedule->command('randomTop')->daily();
        $schedule->command('getExtensionData')->hourly();
        $schedule->command('GetYandexMetrika')->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands() {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

}
