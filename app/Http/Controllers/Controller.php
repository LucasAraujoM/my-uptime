<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class Controller
{
    public function login(Request $request)
    {
        try {
            if (Auth::check()) {
                return redirect()->route('dashboard');
            }
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'remember' => 'boolean',
            ]);
            $key = 'login' . $request->input('email') . '|' . $request->ip();
            if (RateLimiter::tooManyAttempts($key, 5)) {
                RateLimiter::availableIn($key);
                return redirect()->back()->with('error', 'Too many attemps, try again in later.');
            }
            RateLimiter::hit($key);
            if (Auth::attempt($request->only('email', 'password'))) {
                RateLimiter::clear($key);
                return redirect()->route('dashboard');
            }
            return redirect()->back()->with('error', 'Invalid credentials.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('login')->with('error', 'An error occurred, please try again.');
        }
    }
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|max:20',
                'password_confirmation' => 'required|same:password',
            ]);
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => bcrypt($request->input('password')),
            ]);
            Auth::login($user);
            return redirect()->route('dashboard')->with('success', 'User registered successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('register')->with('error', 'An error occurred, please try again.');
        }
    }
    public function logout()
    {
        try {
            Auth::logout();
            return redirect()->route('login')->with('success', 'User logged out successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('login');
        }
    }
}
