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
        $schedule = new Schedule(Schedule::$schedule_example);
        foreach (Schedule::$weekMap as $day => $dayname) {
            if ($day != 'SUN') {
                $schedule->setObjectAttribute('watering_weekdays', $day, false);
                $schedule->setObjectAttribute('watering_weekdays_frequency', $day, '');
                $schedule->setObjectAttribute('watering_weekdays_time_hours', $day, '');
                $schedule->setObjectAttribute('watering_weekdays_time_minutes', $day, '');
                $schedule->setObjectAttribute('watering_weekdays_duration_hours', $day, '');
                $schedule->setObjectAttribute('watering_weekdays_duration_minutes', $day, '');
            }
        }
        $schedule->at_creation = true;
        $view_data = [
            'schedule' => $schedule,
        ];
        return view('models.schedule.index')->with($view_data);
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
            'watering_weekdays_time_hours' => 'required',
            'watering_weekdays_time_minutes' => 'required',
            'watering_weekdays_duration_hours' => 'required',
            'watering_weekdays_duration_minutes' => 'required',
        ]);
        $schedule = new Schedule($validated);
        $schedule->user_id = $user_id;
        $schedule->save();
        return $schedule;
    }

    public function edit(Schedule $schedule)
    {
        return view('models.schedule.index')->with(['schedule' => $schedule]);
    }

    public function update(Schedule $schedule, Request $request)
    {
        if ($request->user() == null) {
            return redirect('schedule.create');
        }
        $validated = $request->validate([
            'name' => 'required|max:255',
            'watering_weekdays' => 'required',
            'watering_weekdays_frequency' => 'required',
            'watering_weekdays_time_hours' => 'required',
            'watering_weekdays_time_minutes' => 'required',
            'watering_weekdays_duration_hours' => 'required',
            'watering_weekdays_duration_minutes' => 'required',
        ]);
        $schedule->fill($validated);
        $schedule->update();
        return $schedule;
    }

    public function destroy()
    {
    }
}
