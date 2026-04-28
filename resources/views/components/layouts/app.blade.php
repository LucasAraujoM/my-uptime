<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-900" data-theme="myuptime">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @yield('head')
</head>

<body class="h-full font-sans antialiased text-gray-100 bg-gray-900 selection:bg-purple-500 selection:text-white">
    <!-- Mobile navbar -->
    <x-nav sticky class="lg:hidden bg-gray-900/90 backdrop-blur-xl border-b border-gray-800">
        <x-slot:brand>
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-8 h-8 rounded-lg object-contain">
                <span class="font-bold text-xl tracking-tight">MyUptime</span>
            </div>
        </x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="lg:hidden me-3 p-2 rounded-lg hover:bg-gray-800 transition-colors">
                <x-icon name="o-bars-3" class="w-6 h-6 text-gray-400" />
            </label>
        </x-slot:actions>
    </x-nav>

    <x-main>
        <!-- Sidebar -->
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-gray-900 border-r border-gray-800 w-64">
            <!-- Logo/Brand -->
            <div class="p-4 pb-2">
                <x-app-brand />
            </div>

            @if($user = auth()->user())
                <x-menu activate-by-route separator="false" class="px-2 gap-1">
                    <!-- User info -->
                    <x-list-item :item="$user" value="name" sub-value="email"
                        class="!rounded-xl mb-6 !p-3 hover:bg-gray-800/50 text-white">
                        <x-slot:avatar>
                            <x-avatar placeholder="{{ substr($user->name, 0, 1) }}"
                                class="!w-9 !h-9 bg-gray-700 text-gray-200" />
                        </x-slot:avatar>
                    </x-list-item>

                    <!-- Main Navigation -->
                    <div
                        class="hidden-when-collapsed text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mb-2">
                        Main</div>

                    <x-menu-item title="Dashboard" icon="o-home" link="/dashboard"
                        active-class="!bg-purple-500/10 !text-purple-400" />

                    <!-- Monitoring Section -->
                    <div
                        class="hidden-when-collapsed text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mb-2 mt-6">
                        Monitoring</div>

                    <x-menu-item title="Monitors" icon="o-server" link="/monitors"
                        active-class="!bg-purple-500/10 !text-purple-400" />
                    <x-menu-item title="Add Monitor" icon="o-plus" link="/monitor/add" />
                    <x-menu-item title="Incidents" icon="o-exclamation-circle" link="/incidents" />

                    <!-- Settings Section -->
                    <div
                        class="hidden-when-collapsed text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mb-2 mt-6">
                        Settings</div>

                    <x-menu-item title="General" icon="o-adjustments-horizontal" link="/settings" />
                    <x-menu-item title="Alerts" icon="o-bell" link="/alert-settings" />
                    <x-menu-item title="Team" icon="o-users" link="/team" />
                    <x-menu-item title="Billing" icon="o-credit-card" link="/billing" />

                    <!-- Logout -->
                    <x-menu-item title="Logout" icon="o-arrow-right-on-rectangle" link="/logout"
                        class="!text-red-400 hover:!bg-red-500/10" />
                </x-menu>
            @endif
        </x-slot:sidebar>

        <!-- Content -->
        <x-slot:content class="bg-gray-900">
            <div class="max-w-7xl mx-auto p-4 lg:p-8">
                {{ $slot }}
            </div>
        </x-slot:content>
    </x-main>

    <x-toast />
    @yield('scripts')
</body>

</html>