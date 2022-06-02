<?php

namespace App\Http\Controllers;

use App\Models\ControlDevice;
use App\Models\SensorDevice;
use App\Models\SensorReading;
use App\Models\WateringEntry;
use Illuminate\Http\Request;

class SensorReadingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(ControlDevice $control_device, WateringEntry $watering_entry, SensorDevice $sensor_device, Request $request)
    {
        if ($request->user() == null) {
            return redirect('schedule.create');
        }
        $validated = $request->validate([
            'measurement_type' => 'required',
            'value' => 'required',
            'measured_at' => 'required',
        ]);
        $sensor_reading = new SensorReading($validated);
        $sensor_reading->sensor_device_id = $sensor_device->id;
        $sensor_reading->watering_entry_id = $watering_entry->id;
        $sensor_reading->save();
        return $sensor_reading;
    }
}
