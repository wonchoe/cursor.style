<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

    protected $commands = [
        Commands\DailyQuote::class,
        Commands\randomTop::class,
        Commands\getExtensionData::class,
        Commands\ReportGetCommand::class,
	    Commands\GetGoogleAnalyticsData::class,
    ];

    protected function schedule(Schedule $schedule) {
        $schedule->command('getExtensionData')->hourly();
        $schedule->command('grubhub')->hourly();
        $schedule->command('grubhub:schedule')->dailyAt('10:40');
        $schedule->command('grubhub:schedule')->dailyAt('15:40');
        $schedule->command('grubhub:schedule_reverse')->dailyAt('10:40');
        $schedule->command('grubhub:schedule_reverse')->dailyAt('15:40');
        $schedule->command('randomTop')->daily();
	    $schedule->command('GetGoogleAnalyticsData')->everyMinute();
        $schedule->command('yandex:report')->everyFiveMinutes();
    }

    protected function commands() {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }

}
