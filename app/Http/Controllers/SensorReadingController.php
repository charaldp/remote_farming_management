<?php

namespace App\Http\Controllers;

use App\Events\SensorReadingAdded;
use App\Models\ControlDevice;
use App\Models\SensorDevice;
use App\Models\SensorReading;
use App\Models\WateringEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

class SensorReadingController extends Controller
{
    public function store(ControlDevice $control_device, WateringEntry $watering_entry, SensorDevice $sensor_device, Request $request)
    {

        $validated = $request->validate([
            'measurement_type' => 'required',
            'value' => 'required',
        ]);
        $sensor_reading = new SensorReading($validated);
        $sensor_reading->sensor_device_id = $sensor_device->id;
        $sensor_reading->watering_entry_id = $watering_entry->id;
        $sensor_reading->save();
        SensorReadingAdded::dispatch($sensor_reading);
        return $sensor_reading;
    }
}
