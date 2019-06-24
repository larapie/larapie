<?php

namespace App\Foundation\Kernels;

use Illuminate\Console\Scheduling\Schedule;
use Larapie\Core\Kernels\ConsoleKernel as Kernel;

class ConsoleKernel extends Kernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        /*
         *
         * DO NOT REMOVE THIS.
         * Defines the schedules from the modules.
         *
         */
        parent::schedule($schedule);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('app/Foundation/Routes/console.php');
    }
}
