<?php

namespace App\Http\Controllers;

use App\Events\ControlDeviceSwitchedIsOnStatus;
use App\Models\ControlDevice;
use App\Models\WateringEntry;
use Illuminate\Http\Request;

class ControlDeviceController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');

    }

    public function create() {
        $control_device = new ControlDevice([]);
        $control_device->at_creation = true;
        return view('models.control_device.index')->with(['control_device' => $control_device]);
    }

    public function store(Request $request) {

    }

    public function edit(ControlDevice $control_device) {
        return view('models.control_device.index')->with(['control_device' => $control_device]);
    }

    public function show(ControlDevice $control_device) {
        $watering_entry = $control_device->watering_entries->last();
        unset($control_device->watering_entries);
        if ($watering_entry) {
            $control_device->watering_entry_id = $watering_entry->id;
        } else {
            $control_device->watering_entry_id = 0;
        }
        return $control_device;
    }

    public function update(ControlDevice $control_device, Request $request) {
        // dd($request);
        if ($request->user() == null) {
            return redirect('schedule.create');
        }
        $is_on_old = $control_device->is_on;
        $validated = $request->validate([
            'is_on' => 'required|boolean',
            'name' => 'required|max:255',
        ]);
        $control_device->fill($validated);
        $control_device->update();
        $is_on_new = $control_device->is_on;
        if (!$is_on_old && $is_on_new) {
            ControlDeviceSwitchedIsOnStatus::dispatch($control_device);
        }
        return $control_device;
    }
}
