<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        $schedule = new Schedule([
            'watering_weekdays' => ['SUN' => true],
            'watering_weekdays_frequency' => ['SUN' => 1],
            'watering_weekdays_time' => ['SUN' => 7200],
            'watering_weekdays_duration' => ['SUN' => 5400],
        ]);
        foreach (Schedule::$weekMap as $day => $dayname) {
            if ($day != 'SUN') {
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
        if ($request->user() == null) {
            return redirect('schedule.create');
        }
        $user_id = $request->user()->id;
        $validated = $request->validate([
            'name' => 'required|max:255',
            'watering_weekdays' => 'required',
            'watering_weekdays_frequency' => 'required',
            'watering_weekdays_time' => 'required',
            'watering_weekdays_duration' => 'required',
        ]);
        $schedule = new Schedule($validated);
        $schedule->user_id = $user_id;
        $schedule->save();
        return $schedule;
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
