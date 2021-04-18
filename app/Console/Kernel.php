<?php

namespace App\Console;

use App\Console\Commands\DeleteTempFile;
use App\Console\Commands\PostToTwitter;
use App\Console\Commands\ShareSocial;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        DeleteTempFile::class,
        ShareSocial::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('delete:temp')
                 ->hourly()
                 ;

        $schedule->command('share:social')
                 ->twiceDaily(17, 22)
                 ->timezone('America/New_York')
                 ;


    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
