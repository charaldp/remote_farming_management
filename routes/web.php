<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScheduleController;

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
