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
<div class="max-w-md mx-auto mt-50 p-6 rounded shadow bg-base-300">
    @include('components.flash.messages')
    <form wire:submit.prevent="register">
        <x-mary-input 
            label="Name" 
            type="text" 
            wire:model.defer="name" 
            required 
            autofocus 
        />
        <x-mary-input 
            label="Email" 
            type="email" 
            wire:model.defer="email" 
            required 
            autofocus 
        />

        <x-password 
            label="Password" 
            wire:model="password" 
            right 
        />

        <x-password 
            label="Confirm Password" 
            wire:model="password_confirmation" 
            right 
        />
        
        <x-mary-button type="submit" class="mt-4 w-full">
            Create Account
        </x-mary-button>
    </form>
    <div class="mt-4">
        <p class="text-center text-sm text-gray-600 mb-4">
            Already have an account?
        </p>
        <x-mary-button class=" w-full" link="{{route('login')}}">
            Login
        </x-mary-button>
    </div>
</div>
