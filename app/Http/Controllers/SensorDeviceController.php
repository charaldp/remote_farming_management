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
        $sensor_device->control_device_id = $control_device->id;
        $sensor_device->control_device = $control_device;
        return view('models.sensor_device.index')->with(['sensor_device' => $sensor_device]);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'type' => 'required|max:255',
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

    public function update(ControlDevice $control_device, SensorDevice $sensor_device, Request $request)
    {
        if ($request->user() == null) {
            return redirect('schedule.create');
        }
        $validated = $request->validate([
            'type' => 'required|',
            'name' => 'required|max:255',
        ]);
        $sensor_device->fill($validated);
        $sensor_device->update();
        return $sensor_device;
    }

}
