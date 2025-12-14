<?php

use App\Models\Monitor;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Carbon\Carbon;

new class extends Component {
    public string $userName;
    public ?Monitor $lastDownMonitor = null;
    public int $totalMonitors = 0;
    public int $totalUpMonitors = 0;
    public int $totalDownMonitors = 0;
    public int $totalPausedMonitors = 0;
    public float $totalUptime = 0.0;
    public float $totalDowntime = 0.0;
    public string $selectedPeriod = '24h';

    public array $chartLabels = [];
    public array $chartUptimeData = [];
    public array $chartDowntimeData = [];

    // ... (keep other properties)

    public function mount(): void
    {
        $user = User::find(Auth::id());
        $this->userName = $user->name;

        $this->loadDashboardData($user);
        $this->loadChartData($user);
    }

    public function changePeriod($period)
    {
        $this->selectedPeriod = $period;
        $this->updatedSelectedPeriod();
    }

    public function updatedSelectedPeriod(): void
    {
        $user = User::find(Auth::id());
        $this->loadDashboardData($user);
        $this->loadChartData($user);
    }
    
    public function loadDashboardData(User $user): void
    {
        $userId = $user->id;

        // Count monitors by status
        $this->totalMonitors = Monitor::where('user_id', $userId)->count();
        $this->totalDownMonitors = Monitor::where('user_id', $userId)->where('status', 'down')->count();
        $this->totalPausedMonitors = Monitor::where('user_id', $userId)->where('status', 'paused')->count();
        $this->totalUpMonitors = max(0, $this->totalMonitors - $this->totalDownMonitors - $this->totalPausedMonitors);

        // Get last down monitor
        $this->lastDownMonitor = Monitor::where('user_id', $userId)
            ->where('status', 'down')
            ->latest('updated_at')
            ->first();

        // Calculate average uptime and downtime based on selected period
        if ($this->totalMonitors > 0) {
            $uptimeField = "uptime_{$this->selectedPeriod}";
            $downtimeField = "downtime_{$this->selectedPeriod}";

            $monitors = Monitor::where('user_id', $userId)->get();

            $totalUptime = $monitors->sum($uptimeField);
            $totalDowntime = $monitors->sum($downtimeField);

            $this->totalUptime = $totalUptime / $this->totalMonitors;
            $this->totalDowntime = $totalDowntime / $this->totalMonitors;
        } else {
            $this->totalUptime = 0;
            $this->totalDowntime = 0;
        }
    }

    protected function loadChartData(User $user): void
    {
        $monitors = Monitor::where('user_id', $user->id)
            ->whereHas('logs')
            ->select('id', 'name', 'status', 'created_at')
            ->limit(10)
            ->get();

        $this->chartLabels = [];
        $this->chartUptimeData = [];
        $this->chartDowntimeData = [];

        foreach ($monitors as $monitor) {
            $this->chartLabels[] = strlen($monitor->name) > 20
                ? substr($monitor->name, 0, 20) . '...'
                : $monitor->name;
            $this->chartUptimeData[] = round($monitor->logs()->where('status', 'up')->count() / $monitor->logs()->count() * 100 ?? 0, 2);
            $this->chartDowntimeData[] = round($monitor->logs()->where('status', 'down')->count() / $monitor->logs()->count() * 100 ?? 0, 2);
        }

        $this->dispatch('chart-updated', [
            'labels' => $this->chartLabels,
            'uptime' => $this->chartUptimeData,
            'downtime' => $this->chartDowntimeData,
        ]);
    }
};
?>

@section('title', 'Dashboard')

@include('components.flash.messages')

<div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-end mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Dashboard</h1>
            <p class="text-gray-400 mt-1">Overview of your monitoring status.</p>
        </div>
        
        <!-- Period Selector -->
        <div class="bg-gray-800/50 p-1 rounded-lg border border-gray-700/50 flex">
            @foreach([
                '12h' => '12h',
                '24h' => '24h',
                '7d' => '7d',
                '30d' => '30d'
            ] as $value => $label)
                <button 
                    wire:click="changePeriod('{{ $value }}')"
                    class="px-3 py-1.5 text-sm font-medium rounded-md transition-all duration-200 {{ $selectedPeriod === $value ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/20' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Monitors -->
        <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-2xl p-5 hover:border-purple-500/30 transition-colors duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-sm font-medium text-gray-400">Total Monitors</h3>
                    <div class="flex items-baseline mt-2">
                        <span class="text-3xl font-bold text-white tracking-tight">{{ $totalMonitors }}</span>
                    </div>
                </div>
                <div class="p-2 bg-gray-700/30 rounded-lg">
                    <x-icon name="o-server" class="w-6 h-6 text-purple-400" />
                </div>
            </div>
        </div>

        <!-- Up Monitors -->
        <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-2xl p-5 hover:border-green-500/30 transition-colors duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-sm font-medium text-gray-400">Operational</h3>
                    <div class="flex items-baseline mt-2">
                        <span class="text-3xl font-bold text-white tracking-tight">{{ $totalUpMonitors }}</span>
                        @if($totalMonitors > 0)
                            <span class="ml-2 text-sm text-green-400 font-medium">
                                {{ round(($totalUpMonitors / $totalMonitors) * 100) }}%
                            </span>
                        @endif
                    </div>
                </div>
                <div class="p-2 bg-green-500/10 rounded-lg">
                    <x-icon name="o-check-circle" class="w-6 h-6 text-green-400" />
                </div>
            </div>
        </div>

        <!-- Down Monitors -->
        <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-2xl p-5 hover:border-red-500/30 transition-colors duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-sm font-medium text-gray-400">Downtime</h3>
                    <div class="flex items-baseline mt-2">
                        <span class="text-3xl font-bold text-white tracking-tight">{{ $totalDownMonitors }}</span>
                        @if($totalDownMonitors > 0)
                            <span class="ml-2 text-sm text-red-400 animate-pulse">Action Needed</span>
                        @endif
                    </div>
                </div>
                <div class="p-2 bg-red-500/10 rounded-lg">
                    <x-icon name="o-x-circle" class="w-6 h-6 text-red-400" />
                </div>
            </div>
        </div>

        <!-- Avg Uptime -->
        <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-2xl p-5 hover:border-blue-500/30 transition-colors duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-sm font-medium text-gray-400">Avg Uptime</h3>
                    <div class="flex items-baseline mt-2">
                        <span class="text-3xl font-bold text-white tracking-tight">{{ number_format($totalUptime, 2) }}%</span>
                    </div>
                </div>
                <div class="p-2 bg-blue-500/10 rounded-lg">
                    <x-icon name="o-chart-bar" class="w-6 h-6 text-blue-400" />
                </div>
            </div>
            <!-- Simple progress bar -->
            <div class="w-full bg-gray-700/50 rounded-full h-1.5 mt-3">
                <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $totalUptime }}%"></div>
            </div>
        </div>
    </div>

    <!-- Main Content Split -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Chart Section (2 cols) -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-2xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Performance Trend</h2>
                        <p class="text-sm text-gray-400">Uptime vs Downtime over monitored services</p>
                    </div>
                </div>

                @if(count($chartLabels) > 0)
                <div class="h-80 relative w-full" 
                     x-data="{
                        chart: null,
                        init() {
                            const ctx = this.$refs.canvas.getContext('2d');
                            
                            // Gradients
                            const gradientUp = ctx.createLinearGradient(0, 0, 0, 300);
                            gradientUp.addColorStop(0, 'rgba(168, 85, 247, 0.2)'); // Purple
                            gradientUp.addColorStop(1, 'rgba(168, 85, 247, 0)');

                            const gradientDown = ctx.createLinearGradient(0, 0, 0, 300);
                            gradientDown.addColorStop(0, 'rgba(239, 68, 68, 0.2)'); // Red
                            gradientDown.addColorStop(1, 'rgba(239, 68, 68, 0)');

                            this.chart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: @js($chartLabels),
                                    datasets: [
                                        {
                                            label: 'Uptime',
                                            backgroundColor: gradientUp,
                                            borderColor: '#a855f7',
                                            borderWidth: 2,
                                            data: @js($chartUptimeData),
                                            fill: true,
                                            tension: 0.4,
                                            pointRadius: 0,
                                            pointHoverRadius: 4
                                        },
                                        {
                                            label: 'Downtime',
                                            backgroundColor: gradientDown,
                                            borderColor: '#ef4444',
                                            borderWidth: 2,
                                            data: @js($chartDowntimeData),
                                            fill: true,
                                            tension: 0.4,
                                            pointRadius: 0,
                                            pointHoverRadius: 4
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    interaction: {
                                        mode: 'index',
                                        intersect: false,
                                    },
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        tooltip: {
                                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                            titleColor: '#fff',
                                            bodyColor: '#cbd5e1',
                                            borderColor: 'rgba(255,255,255,0.1)',
                                            borderWidth: 1,
                                            padding: 10,
                                            displayColors: true,
                                            usePointStyle: true
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            max: 100,
                                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                            ticks: { color: '#6b7280', font: {family: 'Instrument Sans'} }
                                        },
                                        x: {
                                            grid: { display: false },
                                            ticks: { color: '#6b7280', font: {family: 'Instrument Sans'}, maxRotation: 0, autoSkip: true, maxTicksLimit: 6 }
                                        }
                                    }
                                }
                            });

                            Livewire.on('chart-updated', (data) => {
                                if (this.chart) {
                                    this.chart.data.labels = data.labels;
                                    this.chart.data.datasets[0].data = data.uptime;
                                    this.chart.data.datasets[1].data = data.downtime;
                                    this.chart.update();
                                }
                            });
                        }
                     }"
                     wire:ignore
                >
                    <canvas x-ref="canvas"></canvas>
                </div>
                <!-- Custom Legend -->
                <div class="flex items-center justify-center gap-6 mt-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-purple-500"></div>
                        <span class="text-sm text-gray-400">Uptime %</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <span class="text-sm text-gray-400">Downtime %</span>
                    </div>
                </div>
                @else
                    <div class="h-64 flex flex-col items-center justify-center text-gray-500">
                        <div class="p-4 rounded-full bg-gray-800 mb-3">
                            <x-icon name="o-chart-bar" class="w-8 h-8 opacity-50" />
                        </div>
                        <p class="font-medium">No sufficient data for chart</p>
                        <x-button label="Add Monitor" link="{{ route('add-monitor') }}" class="btn-sm btn-ghost mt-2 text-purple-400" />
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
             <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('add-monitor') }}" class="group bg-gray-800/40 border border-gray-700/50 p-4 rounded-2xl hover:bg-gray-800 transition-all flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-icon name="o-plus" class="w-6 h-6 text-purple-400" />
                    </div>
                    <div>
                        <div class="font-semibold text-white">Add Monitor</div>
                        <div class="text-sm text-gray-500">Start tracking a new URL</div>
                    </div>
                </a>
                
                <a href="{{ route('alert-settings') }}" class="group bg-gray-800/40 border border-gray-700/50 p-4 rounded-2xl hover:bg-gray-800 transition-all flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-orange-500/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-icon name="o-bell" class="w-6 h-6 text-orange-400" />
                    </div>
                    <div>
                        <div class="font-semibold text-white">Alert Rules</div>
                        <div class="text-sm text-gray-500">Configure notifications</div>
                    </div>
                </a>
             </div>
        </div>

        <!-- Right Col -->
        <div class="space-y-6">
            <!-- Last Status -->
            <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-2xl p-6">
                 <h2 class="text-lg font-semibold text-white mb-4">System Status</h2>
                 
                 @if($lastDownMonitor)
                    <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 mb-4">
                        <div class="flex items-start gap-3">
                            <x-icon name="o-exclamation-circle" class="w-6 h-6 text-red-400 shrink-0" />
                            <div>
                                <h4 class="font-semibold text-red-200">Incident Detected</h4>
                                <p class="text-sm text-red-300/80 mt-1">
                                    {{ $lastDownMonitor->name }} is currently down.
                                </p>
                                <div class="mt-3">
                                    <a href="{{ route('edit-monitor', $lastDownMonitor->id) }}" class="text-xs font-semibold bg-red-500/20 text-red-300 px-3 py-1.5 rounded-lg hover:bg-red-500/30 transition-colors">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                 @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-green-500/10 rounded-full flex items-center justify-center mx-auto mb-3">
                            <x-icon name="o-check" class="w-8 h-8 text-green-500" />
                        </div>
                        <h4 class="text-white font-medium">All systems operational</h4>
                         <p class="text-sm text-gray-500 mt-1">No active incidents reported.</p>
                    </div>
                 @endif

                 <div class="border-t border-gray-700/50 pt-4 mt-4">
                    <div class="flex justify-between items-center text-sm mb-2">
                        <span class="text-gray-400">Monitors Up</span>
                        <span class="text-white font-medium">{{ $totalUpMonitors }}</span>
                    </div>
                     <div class="flex justify-between items-center text-sm mb-2">
                        <span class="text-gray-400">Monitors Down</span>
                        <span class="text-white font-medium">{{ $totalDownMonitors }}</span>
                    </div>
                     <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-400">Monitors Paused</span>
                        <span class="text-white font-medium">{{ $totalPausedMonitors }}</span>
                    </div>
                 </div>
            </div>

            <!-- Recent Activity / Tips -->
            <div class="bg-gradient-to-br from-indigo-900/40 to-purple-900/40 border border-indigo-500/20 rounded-2xl p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-purple-500/20 blur-3xl rounded-full -mr-16 -mt-16 pointer-events-none"></div>
                
                <h3 class="font-semibold text-white mb-2 relative z-10">Pro Tip</h3>
                <p class="text-sm text-indigo-200 relative z-10 leading-relaxed">
                    Check your monitors frequently by setting the interval to 1 minute to catch downtime faster.
                </p>
                 <x-button label="Check Settings" link="{{ route('monitors') }}" class="btn-sm bg-indigo-500 border-none text-white hover:bg-indigo-600 mt-4 relative z-10 w-full" />
            </div>
        </div>
    </div>
</div>