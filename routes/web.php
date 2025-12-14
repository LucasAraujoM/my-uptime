<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::group(['middleware' => 'guest'], function () {
    Volt::route('/login', 'auth.login')->name('login');
    Volt::route('/register', 'auth.register')->name('register');
});
Route::group(['middleware' => 'auth'], function () {
    Route::get('/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out.')->with('timeout', 10);
    })->name('logout');

    Volt::route('/users', 'auth.index')->name('users');
    Volt::route('/team', 'auth.index')->name('team'); // Alias for users

    Volt::route('/dashboard', 'dashboard')->name('dashboard');
    Volt::route('/monitors', 'monitors.listmonitors')->name('monitors');
    Volt::route('/monitor/add', 'monitors.monitor')->name('add-monitor');
    Volt::route('/monitor/edit/{id}', 'monitors.monitor')->name('edit-monitor');

    Volt::route('/incidents', 'incidents')->name('incidents');
    Volt::route('/alert-settings', 'alert-settings')->name('alert-settings');
});
//Volt::route('/monitor/delete/{id}','monitors.delete');