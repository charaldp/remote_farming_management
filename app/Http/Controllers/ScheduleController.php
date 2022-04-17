<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function create()
    {
        $schedule = new Schedule([
            'watering_weekdays' => ['SU' => true],
            'watering_weekdays_frequency' => ['SU' => 1],
            'watering_weekdays_time' => ['SU' => 7200],
            'watering_weekdays_duration' => ['SU' => 5400],
        ]);
        foreach (Schedule::$weekMap as $day => $dayname) {
            if ($day != 'SU') {
                $schedule->setObjectAttribute('watering_weekdays', $day, false);
                $schedule->setObjectAttribute('watering_weekdays_frequency', $day, '');
                $schedule->setObjectAttribute('watering_weekdays_time', $day, '');
                $schedule->setObjectAttribute('watering_weekdays_duration', $day, '');
            }
        }
        $view_data = [
            'schedule' => $schedule,
        ];
        return view('schedule.index')->with($view_data);
    }

    public function store(Request $request)
    {
        dd($request, $request->user());
    }

    public function edit(Schedule $schedule)
    {
        return view('schedule.index')->with(['schedule' => $schedule]);
    }

    public function update()
    {
    }

    public function destroy()
    {
    }
}
