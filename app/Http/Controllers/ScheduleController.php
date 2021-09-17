<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\User;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function create() {
        $schedule = new Schedule([
            'watering_weekdays' => ['0' => '2'],
            'watering_weekdays_frequency' => ['0' => 1],
            'watering_weekdays_time' => ['0' => 7200],
            'watering_weekdays_duration' => ['0' => 5400],
        ]);
        $view_data = [

        ];
        return view('schedule.index')->with(['schedule' => $schedule]);
    }

    public function store(Request $request) {

        $user = User::find(Auth::user()->id);
        dd($user, $request);
    }

    public function edit(Schedule $schedule) {
        return view('schedule.index')->with(['schedule' => $schedule]);
    }

    public function update() {

    }

    public function destroy() {

    }

}
