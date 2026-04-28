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
    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out.')->with('timeout', 10);
    })->name('logout');

    // Email verification routes
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('dashboard')->with('success', 'Email verified successfully.');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Verification link sent!');
    })->name('verification.send');

    // Protected routes (require verified email)
    Route::group(['middleware' => 'verified'], function () {
        Volt::route('/users', 'auth.index')->name('users');
        Volt::route('/team', 'auth.index')->name('team'); // Alias for users

        Volt::route('/dashboard', 'dashboard')->name('dashboard');
        Volt::route('/monitors', 'monitors.listmonitors')->name('monitors');
        Volt::route('/monitor/add', 'monitors.monitor')->name('add-monitor');
        Volt::route('/monitor/edit/{id}', 'monitors.monitor')->name('edit-monitor');

        Volt::route('/incidents', 'incidents')->name('incidents');
        Volt::route('/alert-settings', 'alert-settings')->name('alert-settings');
        Volt::route('/settings', 'settings')->name('settings');
        Volt::route('/billing', 'billing')->name('billing');
    });
});
//Volt::route('/monitor/delete/{id}','monitors.delete');