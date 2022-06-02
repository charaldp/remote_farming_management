<?php

namespace App\Http\Controllers;

use App\Models\ControlDevice;
use Illuminate\Http\Request;

class ControlDeviceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

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
        return $control_device;
    }

    public function update(ControlDevice $control_device, Request $request) {
        if ($request->user() == null) {
            return redirect('schedule.create');
        }
        $validated = $request->validate([
            'is_on' => 'required|boolean',
            'name' => 'required|max:255',
        ]);
        $control_device->fill($validated);
        $control_device->update();
        return $control_device;
    }
}
