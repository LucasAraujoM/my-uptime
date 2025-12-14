<?php

use App\Http\Controllers\Controller;
use Livewire\Volt\Component;
use Illuminate\Http\Request;

new class extends Component {
    //
    public $title;
    public $email;
    public $password;
    public $remember = false;

    public function mount()
    {
        $this->title = 'Login';
    }
    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
            'remember' => 'boolean',
        ]);
        $request = new Request([
            'email' => $this->email,
            'password' => $this->password,
            'remember' => $this->remember,
        ]);
        $controller = new Controller();
        $response = $controller->login($request);
        if ($response) {
            return $response;
        }
    }
}; ?>
@section('title', 'Login')
<div class="flex items-center justify-center min-h-[calc(100vh-100px)]">
    <div
        class="w-full max-w-md p-8 rounded-2xl bg-gray-900/40 backdrop-blur-xl border border-gray-700/50 shadow-2xl relative">
        <div class="text-center mb-8">
            <div
                class="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold text-3xl shadow-lg shadow-purple-900/20 mx-auto mb-6">
                M
            </div>
            <h2 class="text-3xl font-bold text-white tracking-tight"
                style="font-family: 'Instrument Sans', sans-serif;">Welcome Back</h2>
            <p class="text-gray-400 mt-2">Sign in to your dashboard</p>
        </div>

        @include('components.flash.messages')

        <form wire:submit.prevent="login" class="space-y-6">
            <x-input label="Email" icon="o-envelope" type="email" wire:model="email" required autofocus
                class="bg-gray-900/50 border-gray-700 text-white placeholder-gray-500 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" />

            <x-input label="Password" icon="o-key" type="password" wire:model="password" required
                class="bg-gray-900/50 border-gray-700 text-white placeholder-gray-500 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" />

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <x-checkbox wire:model="remember" label="Remember Me" class="checkbox-primary text-gray-300" />
                </div>
                <a href="#" class="text-sm font-medium text-purple-400 hover:text-purple-300 transition-colors">Forgot
                    Password?</a>
            </div>

            <x-button type="submit" label="Sign In" icon="o-arrow-right"
                class="w-full bg-purple-600 hover:bg-purple-700 text-white border-none shadow-lg shadow-purple-900/20"
                spinner="login" />
        </form>

        <div class="mt-8 pt-6 border-t border-gray-700/50 text-center">
            <p class="text-sm text-gray-400 mb-4">
                Don't have an account?
            </p>
            <x-button link="{{ route('register') }}" label="Create Account"
                class="w-full btn-ghost text-white border border-gray-700 hover:bg-gray-800" />
        </div>

        <div class="mt-6 text-center">
            <a href="/"
                class="text-gray-500 hover:text-gray-300 text-sm transition-colors flex items-center justify-center gap-2">
                <x-icon name="o-arrow-left" class="w-4 h-4" /> Back to Home
            </a>
        </div>
    </div>
</div>