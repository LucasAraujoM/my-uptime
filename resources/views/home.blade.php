<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Monitor your API's uptime with our reliable checker service. Get instant alerts and detailed analytics.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>MyUptime - Monitor Your API's Uptime 24/7</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        html {
            scroll-behavior: smooth;
            overflow-x: hidden;
            max-width: 100vw;
        }

        body {
            background: #0a0e1a;
            overflow-x: hidden;
            max-width: 100vw;
            position: relative;
        }

        /* Animated gradient background */
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Glassmorphism effect */
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .glass-strong {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        /* Floating animation */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        /* Glow effect */
        .glow {
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.4),
                0 0 40px rgba(102, 126, 234, 0.2);
        }

        .glow-hover {
            transition: all 0.3s ease;
        }

        .glow-hover:hover {
            box-shadow: 0 0 30px rgba(102, 126, 234, 0.6),
                0 0 60px rgba(102, 126, 234, 0.3);
            transform: translateY(-5px);
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-family: 'Space Grotesk', sans-serif;
        }

        /* Feature card hover effect */
        .feature-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .feature-card:hover {
            transform: translateY(-10px) scale(1.02);
        }

        /* Pricing card special effects */
        .pricing-card {
            position: relative;
            transition: all 0.3s ease;
        }

        .pricing-card:hover {
            transform: translateY(-10px);
        }

        .pricing-card.featured {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        }

        /* Button animations */
        .btn-primary {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-primary:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary:hover {
            transform: scale(1.05);
        }

        /* Pulse animation for icons */
        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.8;
                transform: scale(1.1);
            }
        }

        .pulse-animation {
            animation: pulse 2s ease-in-out infinite;
        }

        /* Navbar blur effect */
        .navbar-blur {
            background: rgba(10, 14, 26, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Stats counter animation */
        @keyframes countUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-item {
            animation: countUp 0.6s ease-out forwards;
        }

        /* Decorative elements */
        .decorative-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.3;
            pointer-events: none;
        }
    </style>
</head>

<body class="text-white">
    <!-- Decorative background blobs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none" style="z-index: -1;">
        <div class="decorative-blob" style="width: 500px; height: 500px; background: #667eea; top: -200px; left: -200px;">
        </div>
        <div class="decorative-blob"
            style="width: 400px; height: 400px; background: #764ba2; top: 50%; right: -150px;">
        </div>
        <div class="decorative-blob"
            style="width: 600px; height: 600px; background: #f093fb; bottom: -300px; left: 50%;">
        </div>
    </div>

    <!-- Navigation -->
    <header class="navbar-blur fixed w-full top-0 z-50">
        <nav class="container mx-auto px-4 py-4 md:py-5">
            <div class="flex flex-wrap md:flex-nowrap justify-between items-center">
                <div class="flex justify-between items-center w-full md:w-auto">
                    <div class="text-2xl font-bold gradient-text" style="font-family: 'Space Grotesk', sans-serif;">
                        MyUptime</div>
                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn"
                        class="md:hidden text-gray-300 hover:text-white focus:outline-none p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                    </button>
                </div>

                <div id="mobile-menu"
                    class="hidden w-full md:flex md:w-auto flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-8 items-center mt-4 md:mt-0 transition-all duration-300">
                    <a href="#features"
                        class="text-gray-300 hover:text-white transition-colors duration-300 font-medium block text-center md:inline-block">Features</a>
                    <a href="#pricing"
                        class="text-gray-300 hover:text-white transition-colors duration-300 font-medium block text-center md:inline-block">Pricing</a>
                    <a href="#stats"
                        class="text-gray-300 hover:text-white transition-colors duration-300 font-medium block text-center md:inline-block">Stats</a>
                    <a href="{{ route('login') }}"
                        class="text-gray-300 hover:text-white transition-colors duration-300 font-medium block text-center md:inline-block">Login</a>
                    <a href="{{ route('register') }}"
                        class="btn-primary bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-2.5 rounded-full hover:from-purple-700 hover:to-blue-700 font-semibold shadow-lg block text-center md:inline-block w-full md:w-auto">Get
                        Started</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="relative">
        <!-- Hero Section -->
        <section class="container mx-auto px-4 pt-28 pb-16 md:pt-40 md:pb-32">
            <div class="text-center max-w-5xl mx-auto">
                <div class="inline-block glass px-4 py-2 rounded-full mb-8 animate-pulse">
                    <span class="text-sm font-semibold text-purple-300">🚀 Trusted by 10,000+ developers
                        worldwide</span>
                </div>

                <h1 class="text-4xl md:text-7xl font-black mb-6 leading-tight"
                    style="font-family: 'Space Grotesk', sans-serif;">
                    Monitor Your API's<br>
                    <span class="gradient-text">Uptime 24/7</span>
                </h1>

                <p class="text-xl md:text-2xl text-gray-300 mb-12 max-w-3xl mx-auto leading-relaxed">
                    Get instant notifications when your API goes down. Stay ahead with real-time monitoring, detailed
                    analytics, and powerful integrations.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-16">
                    <a href="{{ route('register') }}"
                        class="btn-primary bg-gradient-to-r from-purple-600 to-blue-600 text-white px-10 py-4 rounded-full text-lg font-bold shadow-2xl glow">
                        Start Free Trial
                    </a>
                    <a href="#features"
                        class="glass px-10 py-4 rounded-full text-lg font-semibold hover:bg-white/10 transition-all duration-300">
                        Learn More →
                    </a>
                </div>

                <!-- Stats -->
                <div id="stats" class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto mt-20">
                    <div class="stat-item glass-strong p-6 rounded-2xl">
                        <div class="text-4xl font-bold gradient-text mb-2">99.9%</div>
                        <div class="text-gray-400 text-sm">Uptime SLA</div>
                    </div>
                    <div class="stat-item glass-strong p-6 rounded-2xl" style="animation-delay: 0.1s;">
                        <div class="text-4xl font-bold gradient-text mb-2">10k+</div>
                        <div class="text-gray-400 text-sm">Active Users</div>
                    </div>
                    <div class="stat-item glass-strong p-6 rounded-2xl" style="animation-delay: 0.2s;">
                        <div class="text-4xl font-bold gradient-text mb-2">50M+</div>
                        <div class="text-gray-400 text-sm">Checks/Month</div>
                    </div>
                    <div class="stat-item glass-strong p-6 rounded-2xl" style="animation-delay: 0.3s;">
                        <div class="text-4xl font-bold gradient-text mb-2">&lt;30s</div>
                        <div class="text-gray-400 text-sm">Alert Time</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 md:py-32 relative">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold mb-4" style="font-family: 'Space Grotesk', sans-serif;">
                        Powerful <span class="gradient-text">Features</span>
                    </h2>
                    <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                        Everything you need to keep your APIs running smoothly
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    <div class="feature-card glass-strong p-8 rounded-3xl glow-hover">
                        <div class="text-6xl mb-6 pulse-animation">⚡</div>
                        <h3 class="text-2xl font-bold mb-4" style="font-family: 'Space Grotesk', sans-serif;">Real-time
                            Monitoring</h3>
                        <p class="text-gray-400 leading-relaxed">
                            Check your API's status every minute with our distributed monitoring network across multiple
                            regions.
                        </p>
                    </div>

                    <div class="feature-card glass-strong p-8 rounded-3xl glow-hover">
                        <div class="text-6xl mb-6 pulse-animation" style="animation-delay: 0.5s;">📱</div>
                        <h3 class="text-2xl font-bold mb-4" style="font-family: 'Space Grotesk', sans-serif;">Instant
                            Notifications</h3>
                        <p class="text-gray-400 leading-relaxed">
                            Get alerts via email, Slack, Discord, or custom webhooks the moment something goes wrong.
                        </p>
                    </div>

                    <div class="feature-card glass-strong p-8 rounded-3xl glow-hover">
                        <div class="text-6xl mb-6 pulse-animation" style="animation-delay: 1s;">📊</div>
                        <h3 class="text-2xl font-bold mb-4" style="font-family: 'Space Grotesk', sans-serif;">Detailed
                            Analytics</h3>
                        <p class="text-gray-400 leading-relaxed">
                            View comprehensive uptime statistics, response times, and performance metrics with beautiful
                            charts.
                        </p>
                    </div>

                    <div class="feature-card glass-strong p-8 rounded-3xl glow-hover">
                        <div class="text-6xl mb-6 pulse-animation" style="animation-delay: 0.3s;">🔒</div>
                        <h3 class="text-2xl font-bold mb-4" style="font-family: 'Space Grotesk', sans-serif;">SSL
                            Monitoring</h3>
                        <p class="text-gray-400 leading-relaxed">
                            Never miss an SSL certificate expiration with automatic monitoring and renewal reminders.
                        </p>
                    </div>

                    <div class="feature-card glass-strong p-8 rounded-3xl glow-hover">
                        <div class="text-6xl mb-6 pulse-animation" style="animation-delay: 0.7s;">🌍</div>
                        <h3 class="text-2xl font-bold mb-4" style="font-family: 'Space Grotesk', sans-serif;">Global
                            Coverage</h3>
                        <p class="text-gray-400 leading-relaxed">
                            Monitor from multiple locations worldwide to ensure your API is accessible everywhere.
                        </p>
                    </div>

                    <div class="feature-card glass-strong p-8 rounded-3xl glow-hover">
                        <div class="text-6xl mb-6 pulse-animation" style="animation-delay: 1.2s;">🔌</div>
                        <h3 class="text-2xl font-bold mb-4" style="font-family: 'Space Grotesk', sans-serif;">API Access
                        </h3>
                        <p class="text-gray-400 leading-relaxed">
                            Integrate monitoring data into your own tools with our comprehensive REST API.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="py-20 md:py-32 relative">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold mb-4" style="font-family: 'Space Grotesk', sans-serif;">
                        Simple, <span class="gradient-text">Transparent Pricing</span>
                    </h2>
                    <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                        Choose the perfect plan for your needs. No hidden fees.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    <!-- Basic Plan -->
                    <div class="pricing-card glass-strong p-8 rounded-3xl">
                        <div class="text-sm font-semibold text-purple-400 mb-4">BASIC</div>
                        <div class="mb-6">
                            <span class="text-5xl font-bold">$9</span>
                            <span class="text-gray-400 text-lg">/month</span>
                        </div>
                        <ul class="space-y-4 mb-8 text-gray-300">
                            <li class="flex items-start">
                                <span class="text-green-400 mr-3">✓</span>
                                <span>10 monitored websites</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-400 mr-3">✓</span>
                                <span>1-minute check intervals</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-400 mr-3">✓</span>
                                <span>Email notifications</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-400 mr-3">✓</span>
                                <span>30-day data retention</span>
                            </li>
                        </ul>
                        <a href="{{ route('register') }}"
                            class="block text-center glass-strong px-6 py-3 rounded-full font-semibold hover:bg-white/10 transition-all duration-300">
                            Get Started
                        </a>
                    </div>

                    <!-- Pro Plan (Featured) -->
                    <div
                        class="pricing-card featured glass-strong p-8 rounded-3xl border-2 border-purple-500 relative overflow-hidden">
                        <div
                            class="absolute top-0 right-0 bg-gradient-to-r from-purple-600 to-blue-600 text-white text-xs font-bold px-4 py-1 rounded-bl-2xl">
                            POPULAR
                        </div>
                        <div class="text-sm font-semibold text-purple-400 mb-4">PRO</div>
                        <div class="mb-6">
                            <span class="text-5xl font-bold">$29</span>
                            <span class="text-gray-400 text-lg">/month</span>
                        </div>
                        <ul class="space-y-4 mb-8 text-gray-300">
                            <li class="flex items-start">
                                <span class="text-green-400 mr-3">✓</span>
                                <span>50 monitored websites</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-400 mr-3">✓</span>
                                <span>30-second check intervals</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-400 mr-3">✓</span>
                                <span>All notification channels</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-400 mr-3">✓</span>
                                <span>90-day data retention</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-400 mr-3">✓</span>
                                <span>Full API access</span>
                            </li>
                        </ul>
                        <a href="{{ route('register') }}"
                            class="block text-center btn-primary bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-3 rounded-full font-semibold shadow-lg">
                            Get Started
                        </a>
                    </div>

                    <!-- Enterprise Plan -->
                    <div class="pricing-card glass-strong p-8 rounded-3xl">
                        <div class="text-sm font-semibold text-purple-400 mb-4">ENTERPRISE</div>
                        <div class="mb-6">
                            <span class="text-5xl font-bold">$99</span>
                            <span class="text-gray-400 text-lg">/month</span>
                        </div>
                        <ul class="space-y-4 mb-8 text-gray-300">
                            <li class="flex items-start">
                                <span class="text-green-400 mr-3">✓</span>
                                <span>Unlimited websites</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-400 mr-3">✓</span>
                                <span>10-second check intervals</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-400 mr-3">✓</span>
                                <span>Priority support 24/7</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-400 mr-3">✓</span>
                                <span>Unlimited data retention</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-400 mr-3">✓</span>
                                <span>Custom integrations</span>
                            </li>
                        </ul>
                        <a href="{{ route('register') }}"
                            class="block text-center glass-strong px-6 py-3 rounded-full font-semibold hover:bg-white/10 transition-all duration-300">
                            Get Started
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 md:py-32 relative">
            <div class="container mx-auto px-4">
                <div class="glass-strong p-12 md:p-16 rounded-3xl text-center max-w-4xl mx-auto glow">
                    <h2 class="text-4xl md:text-5xl font-bold mb-6" style="font-family: 'Space Grotesk', sans-serif;">
                        Ready to Get Started?
                    </h2>
                    <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
                        Join thousands of developers who trust MyUptime to keep their APIs running smoothly.
                    </p>
                    <a href="{{ route('register') }}"
                        class="btn-primary inline-block bg-gradient-to-r from-purple-600 to-blue-600 text-white px-12 py-4 rounded-full text-lg font-bold shadow-2xl">
                        Start Your Free Trial
                    </a>
                    <p class="text-sm text-gray-400 mt-4">No credit card required • 14-day free trial</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="glass-strong text-white py-16 border-t border-white/10">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div>
                    <h4 class="text-2xl font-bold gradient-text mb-4" style="font-family: 'Space Grotesk', sans-serif;">
                        MyUptime</h4>
                    <p class="text-gray-400 leading-relaxed">
                        Reliable API monitoring for peace of mind. Stay online, stay informed.
                    </p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Product</h4>
                    <ul class="space-y-3">
                        <li><a href="#features"
                                class="text-gray-400 hover:text-white transition-colors duration-300">Features</a></li>
                        <li><a href="#pricing"
                                class="text-gray-400 hover:text-white transition-colors duration-300">Pricing</a></li>
                        <li><a href="#"
                                class="text-gray-400 hover:text-white transition-colors duration-300">Documentation</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Company</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">About</a>
                        </li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Blog</a>
                        </li>
                        <li><a href="#"
                                class="text-gray-400 hover:text-white transition-colors duration-300">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Legal</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Privacy
                                Policy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Terms of
                                Service</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Cookie
                                Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/10 pt-8 text-center">
                <p class="text-gray-400">&copy; {{ date('Y') }} MyUptime. All rights reserved. Built with ❤️ for
                    developers.</p>
            </div>
        </div>
    </footer>
</body>

</html>