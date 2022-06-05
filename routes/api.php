<?php

use App\Http\Controllers\SensorReadingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/control_device/{control_device}/watering_entry/{watering_entry}/sensor_device/{sensor_device}/sensor_reading/store', [SensorReadingController::class, 'store'])->name('watering_entry.store');

