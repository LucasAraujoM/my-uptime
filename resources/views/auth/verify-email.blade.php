<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Email - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-gray-100 bg-gray-900 selection:bg-purple-500 selection:text-white">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="w-full max-w-md p-8 rounded-2xl bg-gray-900/40 backdrop-blur-xl border border-gray-700/50 shadow-2xl">
            <div class="text-center mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="MyUptime Logo" class="w-16 h-16 mx-auto mb-6 object-contain">
                <h2 class="text-3xl font-bold text-white tracking-tight" style="font-family: 'Instrument Sans', sans-serif;">
                    Verify Your Email
                </h2>
                <p class="text-gray-400 mt-2">We've sent a verification link to your email address</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success mb-6 bg-green-500/10 text-green-400 border-green-500/20">
                    <x-icon name="o-check-circle" class="w-5 h-5" />
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="text-center space-y-6">
                <p class="text-gray-300">
                    Please check your email and click the verification link to continue.
                </p>

                <p class="text-sm text-gray-400">
                    Didn't receive the email?
                </p>

                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <x-button type="submit" label="Resend Verification Email" icon="o-envelope"
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white border-none shadow-lg shadow-purple-900/20" />
                </form>

                <div class="pt-6 border-t border-gray-700/50">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-button type="submit" label="Logout" icon="o-arrow-right-on-rectangle"
                            class="w-full btn-ghost text-gray-400 hover:text-white border border-gray-700 hover:bg-gray-800" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
