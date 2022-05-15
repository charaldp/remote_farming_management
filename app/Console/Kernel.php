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
            foreach ($schedule_item->get_watering_weekdays() as $weekday_key => $is_enabled) {
                if (!$is_enabled) {
                    continue;
                }
                // $datetime = $schedule_item->getWeekdayTimeToTime($weekday_key);
                // $datetime->
                // dd($schedule_item->getWeekdayTimeMinute($weekday_key) . ' ' .
                // $schedule_item->getWeekdayTimeHour($weekday_key) . ' ' .
                // '? ' .
                // '* ' .
                // $weekday_key);
                $schedule->call(function ($schedule_item) {
                    dd($schedule_item->subscribed_devices);
                })->cron(
                    $schedule_item->getWeekdayTimeMinute($weekday_key) . ' ' .
                    $schedule_item->getWeekdayTimeHour($weekday_key) . ' ' .
                    '? ' .
                    '* ' .
                    $weekday_key
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
