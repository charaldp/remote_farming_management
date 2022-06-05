<?php

namespace App\Http\Controllers;

use App\Models\ControlDevice;
use App\Models\SensorDevice;
use App\Models\WateringEntry;
use Illuminate\Http\Request;

class WateringEntryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function sensor_readings(ControlDevice $control_device, WateringEntry $watering_entry, SensorDevice $sensor_device, )
    {
        return ['sensor_readings_data' => $watering_entry->sensor_readings_chart_data()];
    }
}
