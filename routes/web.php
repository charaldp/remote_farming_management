<?php

use Illuminate\Support\Facades\Route;

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

// Route::GET('/scene', 'scene')->name('scene');

Auth::routes();
Route::get('/scene', function () {
    return view('scene');
});
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/scenevue', function () {
    return view('scene_vue');
});