<?php

use App\Http\Controllers\ControlDeviceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SensorDeviceController;
use App\Http\Controllers\SensorReadingController;
use App\Http\Controllers\WateringEntryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('login', 'LoginController@show');
Route::post('login', ['as' => 'login', 'uses' => 'LoginController@do']);
// Route::get('/home', 'HomeController@index')->name('home');
Auth::routes();
Route::get('/home', [HomeController::class, 'index']);

Route::get('/schedule/create', [ScheduleController::class, 'create'])->name('schedule.create');
Route::post('/schedule/store', [ScheduleController::class, 'store'])->name('schedule.store');
Route::get('/schedule/{schedule}/edit', [ScheduleController::class, 'edit'])->name('schedule.edit');
Route::patch('/schedule/{schedule}/update', [ScheduleController::class, 'update'])->name('schedule.update');

Route::get('/control_device/create', [ControlDeviceController::class, 'create'])->name('control_device.create');
Route::get('/control_device/{control_device}/show', [ControlDeviceController::class, 'show'])->name('control_device.show');
Route::get('/control_device/{control_device}/edit', [ControlDeviceController::class, 'edit'])->name('control_device.edit');
Route::patch('/control_device/{control_device}/update', [ControlDeviceController::class, 'update'])->name('control_device.update');

Route::get('/control_device/{control_device}/sensor_device/create', [SensorDeviceController::class, 'create'])->name('sensor_device.create');
Route::post('/control_device/{control_device}/sensor_device/store', [SensorDeviceController::class, 'store'])->name('sensor_device.store');
Route::get('/control_device/{control_device}/sensor_device/{sensor_device}/edit', [SensorDeviceController::class, 'edit'])->name('sensor_device.edit');
Route::patch('/control_device/{control_device}/sensor_device/{sensor_device}/update', [SensorDeviceController::class, 'update'])->name('sensor_device.update');

Route::post('/control_device/{control_device}/watering_entry/store', [WateringEntryController::class, 'store'])->name('watering_entry.store');
Route::get('/control_device/{control_device}/watering_entry/{watering_entry}/edit', [WateringEntryController::class, 'store'])->name('watering_entry.store');
Route::get('/control_device/{control_device}/watering_entry/{watering_entry}/sensor_device/{sensor_device}/sensor_reading', [WateringEntryController::class, 'sensor_reading'])->name('watering_entry.store');
