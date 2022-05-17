<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Models\Schedule as ScheduleTable;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->call(function () {
        //     DB::table('users');
        // })->cron('* * * * *');
        $schedules = ScheduleTable::all();
        foreach ($schedules as $schedule_item) {
            foreach ($schedule_item->getStartCrons() as $weekday_key => $cron) {
                $schedule->call(function ($schedule_item) {
                    // Start Control Device Action
                    dd($schedule_item->subscribed_devices);
                })->cron(
                    $cron->minute . ' ' .
                    $cron->hour . ' ' .
                    '? ' .
                    '* ' .
                    $cron->weekday
                );
            }

            foreach ($schedule_item->getStopCrons() as $weekday_key => $cron) {
                $schedule->call(function ($schedule_item) {
                    // Stop Control Device Action
                    dd($schedule_item->subscribed_devices);
                })->cron(
                    $cron->minute . ' ' .
                    $cron->hour . ' ' .
                    '? ' .
                    '* ' .
                    $cron->weekday
                );
            }
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
