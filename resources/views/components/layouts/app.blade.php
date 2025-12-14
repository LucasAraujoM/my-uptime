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
</head>

<body class="h-full font-sans antialiased text-gray-100 bg-gray-900 selection:bg-purple-500 selection:text-white">

    <x-nav sticky class="lg:hidden bg-gray-900/90 backdrop-blur-xl border-b border-gray-800">
        <x-slot:brand>
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-purple-900/20">
                    M
                </div>
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
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-gray-900 border-r border-gray-800 w-72">
            <!-- Brand -->
            <x-app-brand class="p-6">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-purple-900/20">
                        M
                    </div>
                    <span class="font-bold text-xl tracking-tight text-white mr-3">MyUptime</span>
                </div>
            </x-app-brand>

            @if($user = auth()->user())
                <x-menu activate-by-route class="px-2 gap-1 text-gray-400">
                    <!-- User Profile -->
                    <x-menu-separator />
                    <x-list-item :item="$user" value="name" sub-value="email" link="/users"
                        class="hover:bg-gray-800/50 rounded-xl mb-4 p-2 text-white">
                        <x-slot:avatar>
                            <x-avatar placeholder="{{ substr($user->name, 0, 1) }}"
                                class="!w-9 !h-9 bg-gray-700 text-gray-200" />
                        </x-slot:avatar>
                        <x-slot:actions>
                            <x-icon name="o-cog-6-tooth" class="w-5 h-5 text-gray-500 hover:text-white" />
                        </x-slot:actions>
                    </x-list-item>


                    <x-menu-item title="Dashboard" icon="o-home" link="/dashboard"
                        class="px-3 py-2 rounded-lg hover:bg-gray-800 hover:text-white transition-all duration-200"
                        active-class="!bg-purple-500/10 !text-purple-400" />

                    <x-menu-sub title="Monitoring" icon="o-server-stack" class="text-gray-400 hover:text-white">
                        <x-menu-item title="Monitors" icon="o-list-bullet" link="/monitors"
                            class="px-3 py-2 rounded-lg hover:bg-gray-800 hover:text-white transition-all duration-200" />

                        <x-menu-item title="Add Monitor" icon="o-plus-circle" link="/monitor/add"
                            class="px-3 py-2 rounded-lg hover:bg-gray-800 hover:text-white transition-all duration-200" />

                        <x-menu-item title="Incidents" icon="o-exclamation-triangle" link="/incidents"
                            class="px-3 py-2 rounded-lg hover:bg-gray-800 hover:text-white transition-all duration-200"
                            hidden />
                    </x-menu-sub>

                    <x-menu-sub title="Settings" icon="o-cog-6-tooth" class="text-gray-400 hover:text-white">
                        <x-menu-item title="Alerts" icon="o-bell" link="/alert-settings"
                            class="px-3 py-2 rounded-lg hover:bg-gray-800 hover:text-white transition-all duration-200" />

                        <x-menu-item title="Team" icon="o-users" link="/team"
                            class="px-3 py-2 rounded-lg hover:bg-gray-800 hover:text-white transition-all duration-200" />
                    </x-menu-sub>

                    <x-menu-separator class="my-4 border-gray-800" />

                    <x-menu-item title="Logout" icon="o-arrow-right-on-rectangle" link="/logout"
                        class="px-3 py-2 rounded-lg hover:bg-red-500/10 hover:text-red-400 text-gray-400 transition-all duration-200" />
                </x-menu>
            @endif
        </x-slot:sidebar>

        <x-slot:content class="bg-gray-900 lg:bg-gray-900">
            <div class="max-w-7xl mx-auto p-4 lg:p-8">
                {{ $slot }}
            </div>
        </x-slot:content>
    </x-main>

    <x-toast />
</body>

</html>