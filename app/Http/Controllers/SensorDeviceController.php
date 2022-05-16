<?php

namespace App\Http\Controllers;

use App\Models\ControlDevice;
use App\Models\SensorDevice;
use Illuminate\Http\Request;

class SensorDeviceController extends Controller
{
    public function create(ControlDevice $control_device) {
        $sensor_device = new SensorDevice([]);
        $sensor_device->at_creation = true;
        $sensor_device->control_device = $control_device;
        return view('models.sensor_device.index')->with(['sensor_device' => $sensor_device]);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|max:255',
        ]);
        $control_device = new ControlDevice($validated);
        $control_device->is_on = false;
        $control_device->save();
        return $control_device;
    }

    public function edit(ControlDevice $control_device, SensorDevice $sensor_device) {
        return view('models.sensor_device.index')->with(['sensor_device' => $sensor_device]);
    }
}
