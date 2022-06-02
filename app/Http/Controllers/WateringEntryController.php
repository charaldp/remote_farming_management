<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WateringEntryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function sensor_reading()
    {

    }
}
