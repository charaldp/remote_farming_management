<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function create() {
        $schedule = new Schedule([
            'watering_weekdays' => ['SU' => true],
            'watering_weekdays_frequency' => ['SU' => 1],
            'watering_weekdays_time' => ['SU' => 7200],
            'watering_weekdays_duration' => ['SU' => 5400],
        ]);
        $view_data = [

        ];
        return view('schedule.index')->with(['schedule' => $schedule]);
    }

    public function store(Request $request) {

        $user = Auth::user();
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
