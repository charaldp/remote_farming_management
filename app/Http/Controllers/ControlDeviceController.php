<?php

namespace App\Http\Controllers;

use App\Models\ControlDevice;
use Illuminate\Http\Request;

class ControlDeviceController extends Controller
{
    public function create() {
        $control_device = new ControlDevice([]);
        $control_device->at_creation = true;
        return view('models.control_device.index')->with(['control_device' => $control_device]);
    }

    public function edit(ControlDevice $control_device) {
        return view('models.control_device.index')->with(['control_device' => $control_device]);
    }
}
