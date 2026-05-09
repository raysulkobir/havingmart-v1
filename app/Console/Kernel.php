<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use CoreComponentRepository;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Weekly cache update
        $schedule->call(function () {
            CoreComponentRepository::instantiateShopRepository();
            CoreComponentRepository::initializeCache();
        })->everyMinute();


        // $schedule->call(function () {
        //     CoreComponentRepository::instantiateShopRepository();
        //     CoreComponentRepository::initializeCache();
        // })->weekly()->mondays()->at('02:00'); // প্রতি সোমবার 2:00 AM

        // Daily cleanup (example)
        $schedule->call(function () {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
        })->daily()->at('03:00');
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
