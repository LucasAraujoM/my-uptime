<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Monitor your API's uptime with our reliable checker service">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>MyUptime - Monitor Your API's Uptime</title>
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>

<body class="bg-gray-900 text-white">
    <header class="bg-gray-800 shadow-sm">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-4 md:space-y-0">
                <div class="text-xl font-bold text-blue-400">MyUptime</div>
                <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4 items-center">
                    <a href="#features" class="text-gray-300 hover:text-blue-400">Features</a>
                    <a href="#pricing" class="text-gray-300 hover:text-blue-400">Pricing</a>
                    <a href="{{ route('login') }}" class="text-gray-300 hover:text-blue-400">Login</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-center">Get Started</a>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="container mx-auto px-4 py-16">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-white mb-6">Monitor Your API's Uptime 24/7</h1>
                <p class="text-xl text-gray-300 mb-8">Get instant notifications when your API goes down. Stay on top of your API's performance.</p>
                <a href="{{ route('register') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg text-lg hover:bg-blue-700">Start Monitoring Now</a>
            </div>
        </section>

        <section id="features" class="bg-gray-800 py-16">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12 text-white">Key Features</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="text-blue-400 text-4xl mb-4">⚡</div>
                        <h3 class="text-xl font-semibold mb-2 text-white">Real-time Monitoring</h3>
                        <p class="text-gray-300">Check your API's status every minute</p>
                    </div>
                    <div class="text-center">
                        <div class="text-blue-400 text-4xl mb-4">📱</div>
                        <h3 class="text-xl font-semibold mb-2 text-white">Instant Notifications</h3>
                        <p class="text-gray-300">Get alerts via email or Webhooks (Slack, Discord and more)</p>
                    </div>
                    <div class="text-center">
                        <div class="text-blue-400 text-4xl mb-4">📊</div>
                        <h3 class="text-xl font-semibold mb-2 text-white">Detailed Reports</h3>
                        <p class="text-gray-300">View uptime statistics and performance metrics</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="pricing" class="py-16">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12 text-white">Simple Pricing</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                    <div class="bg-gray-800 p-8 rounded-lg shadow-lg">
                        <h3 class="text-xl font-bold mb-4 text-white">Basic</h3>
                        <div class="text-3xl font-bold mb-4 text-white">$9<span class="text-lg text-gray-400">/mo</span></div>
                        <ul class="space-y-2 mb-8 text-gray-300">
                            <li>✓ 10 websites</li>
                            <li>✓ 1-minute checks</li>
                            <li>✓ Email notifications</li>
                        </ul>
                        <a href="{{ route('register') }}" class="block text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Get Started</a>
                    </div>
                    <div class="bg-gray-800 p-8 rounded-lg shadow-lg border-2 border-blue-400">
                        <h3 class="text-xl font-bold mb-4 text-white">Pro</h3>
                        <div class="text-3xl font-bold mb-4 text-white">$29<span class="text-lg text-gray-400">/mo</span></div>
                        <ul class="space-y-2 mb-8 text-gray-300">
                            <li>✓ 50 websites</li>
                            <li>✓ 30-second checks</li>
                            <li>✓ All notifications</li>
                            <li>✓ API access</li>
                        </ul>
                        <a href="{{ route('register') }}" class="block text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Get Started</a>
                    </div>
                    <div class="bg-gray-800 p-8 rounded-lg shadow-lg">
                        <h3 class="text-xl font-bold mb-4 text-white">Enterprise</h3>
                        <div class="text-3xl font-bold mb-4 text-white">$99<span class="text-lg text-gray-400">/mo</span></div>
                        <ul class="space-y-2 mb-8 text-gray-300">
                            <li>✓ Unlimited websites</li>
                            <li>✓ 10-second checks</li>
                            <li>✓ Priority support</li>
                            <li>✓ Custom features</li>
                        </ul>
                        <a href="{{ route('register') }}" class="block text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Get Started</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h4 class="text-lg font-semibold mb-4">MyUptime</h4>
                    <p class="text-gray-400">Reliable API monitoring for peace of mind.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Product</h4>
                    <ul class="space-y-2">
                        <li><a href="#features" class="text-gray-400 hover:text-white">Features</a></li>
                        <li><a href="#pricing" class="text-gray-400 hover:text-white">Pricing</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Company</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">About</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} MyUptime. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>