<?php

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Volt\Component;

new class extends Component {
    //
    public $title;
    public $email;
    public $password;
    public $password_confirmation;
    public $name;
    public $remember = false;

    public function register()
    {
        $this->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        try {
            $request = new Request();
            $request->merge([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
            ]);
            $controller = new Controller();
            $response = $controller->register($request);
            return $response;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('register')->with('error', 'An error occurred, please try again.');
        }
    }
}; ?>
@section('title', 'Register')
<div class="flex items-center justify-center min-h-[calc(100vh-100px)]">
    <div
        class="w-full max-w-md p-8 rounded-2xl bg-gray-900/40 backdrop-blur-xl border border-gray-700/50 shadow-2xl relative">
        <div class="text-center mb-8">
            <div
                class="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold text-3xl shadow-lg shadow-purple-900/20 mx-auto mb-6">
                M
            </div>
            <h2 class="text-3xl font-bold text-white tracking-tight"
                style="font-family: 'Instrument Sans', sans-serif;">Create Account</h2>
            <p class="text-gray-400 mt-2">Start monitoring your services today</p>
        </div>

        @include('components.flash.messages')

        <form wire:submit.prevent="register" class="space-y-6">
            <x-input label="Name" icon="o-user" type="text" wire:model="name" required autofocus
                class="bg-gray-900/50 border-gray-700 text-white placeholder-gray-500 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" />

            <x-input label="Email" icon="o-envelope" type="email" wire:model="email" required
                class="bg-gray-900/50 border-gray-700 text-white placeholder-gray-500 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" />

            <x-input label="Password" icon="o-key" type="password" wire:model="password" required
                class="bg-gray-900/50 border-gray-700 text-white placeholder-gray-500 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" />

            <x-input label="Confirm Password" icon="o-check-circle" type="password" wire:model="password_confirmation"
                required
                class="bg-gray-900/50 border-gray-700 text-white placeholder-gray-500 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" />

            <x-button type="submit" label="Get Started" icon="o-rocket-launch"
                class="w-full bg-purple-600 hover:bg-purple-700 text-white border-none shadow-lg shadow-purple-900/20"
                spinner="register" />
        </form>

        <div class="mt-8 pt-6 border-t border-gray-700/50 text-center">
            <p class="text-sm text-gray-400 mb-4">
                Already have an account?
            </p>
            <x-button link="{{ route('login') }}" label="Sign In"
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