<?php

use App\Http\Controllers\Controller;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;

Route::get('/home', function () {
    return view('home');
});
Route::group(['middleware' => 'guest'], function () {
    Volt::route('/login', 'auth.login')->name('login');
    Volt::route('/register', 'auth.register')->name('register');
});
Route::group(['middleware' => 'auth'], function () {
    Volt::route('/users', 'auth.index');
    Volt::route('/', 'dashboard')->name('dashboard')->middleware('auth');
    Volt::route('/monitors', 'monitors.listmonitors');
    Volt::route('/monitor/add', 'monitors.monitor');
    Volt::route('/monitor/edit/{id}', 'monitors.monitor')->name('edit-monitor');
});
//Volt::route('/monitor/delete/{id}','monitors.delete');