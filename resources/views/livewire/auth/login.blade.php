<?php

use Livewire\Volt\Component;

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
}; ?>
@section('title', 'Login')
<div class="max-w-md mx-auto mt-50 p-6 rounded shadow bg-base-300">
    <form wire:submit.prevent="login">
        <x-mary-input 
            label="Email" 
            type="email" 
            wire:model.defer="email" 
            required 
            autofocus 
        />
        
        <x-mary-input 
            label="Password" 
            type="password" 
            wire:model.defer="password" 
            required 
        />
        
        <div class="mt-4 flex items-center">
            <x-checkbox wire:model="remember" />
            <label for="remember" class="ml-2 text-sm text-gray-600 cursor-pointer">
                Remember Me
            </label>
        </div>
        
        <x-mary-button type="submit" class="mt-4 w-full">
            Login
        </x-mary-button>
    </form>
    <div class="mt-4">
        <p class="text-center text-sm text-gray-600 mb-4">
            Don't have an account?
        </p>
        <x-mary-button class=" w-full" link="{{route('register')}}">
            Register
        </x-mary-button>
    </div>
</div>
