<?php

use App\Models\Monitor;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Carbon\Carbon;

new class extends Component
{
    public string $userName;
    public ?Monitor $lastDownMonitor = null;
    public int $totalMonitors = 0;
    public int $totalDownMonitors = 0;
    public int $totalPausedMonitors = 0;
    public float $totalUptime = 0.0;
    public float $totalDowntime = 0.0;
    public string $timeRange = '24 hours';

    public array $myChart = [
        'type' => 'line',
        'data' => [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Uptime (hours)',
                    'backgroundColor' => 'rgba(34,197,94,0.2)',
                    'borderColor' => 'rgba(34,197,94,1)',
                    'data' => [],
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
        ],
        'options' => [
            'responsive' => true,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => ['display' => true, 'text' => 'Hours'],
                ],
                'x' => [
                    'title' => ['display' => true, 'text' => 'Time'],
                ],
            ],
        ],
    ];

    public function mount(): void
    {
        $user = User::find(Auth::user()->id);

        $this->userName = $user->name;
        $userId = $user->id;

        $this->totalMonitors = Monitor::where('user_id', $userId)->count();
        $this->totalDownMonitors = Monitor::where('user_id', $userId)->where('status', 'down')->count();
        $this->totalPausedMonitors = Monitor::where('user_id', $userId)->where('status', 'paused')->count();

        $this->lastDownMonitor = Monitor::where('user_id', $userId)
            ->where('status', 'down')
            ->latest('updated_at')
            ->first();

        $this->totalUptime = $user->uptimes()->sum('uptime_12h') / $user->uptimes()->count('uptime_12h');
        $this->totalDowntime = $user->uptimes()->sum('downtime_12h') / $user->uptimes()->count('downtime_12h');

        $this->loadUptimeChartData();
    }

    public function updatedTimeRange($value): void
    {
        $this->timeRange = is_array($value) ? (string)$value[0] : (string)$value;
        $this->loadUptimeChartData();
    }

    protected function loadUptimeChartData(): void
    {
        // Generate dummy data based on time range
        $now = Carbon::now();
        $startDate = match ($this->timeRange) {
            '7 days' => $now->copy()->subDays(7),
            '30 days' => $now->copy()->subDays(30),
            default => $now->copy()->subDay(),
        };

        $labels = [];
        $data = [];
        $current = $startDate;

        // Generate data points based on time range
        while ($current <= $now) {
            $labels[] = $this->timeRange === '24 hours' 
                ? $current->format('H:i')
                : $current->format('M d');

            // Generate random uptime between 10-12 hours
            $data[] = rand(100, 120) / 10;

            // Increment based on time range
            $current = match ($this->timeRange) {
                '24 hours' => $current->addHour(),
                '7 days' => $current->addDay(),
                '30 days' => $current->addDays(2),
            };
        }

        Arr::set($this->myChart, 'data.labels', $labels);
        Arr::set($this->myChart, 'data.datasets.0.data', $data);
    }
};
?>

@section('title', 'Dashboard')

@include('components.flash.messages')

<div class="container mx-auto px-4 py-8">
    <x-header title="Dashboard" subtitle="Real-time overview of your system status and performance metrics" separator />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Last Down Monitor -->
        <div class="rounded-xl shadow-lg p-6 border-l-4 border-red-500 hover:-translate-y-1 transition-transform duration-300 bg-base-100">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Last Down Monitor</h2>
                <div class="w-10 h-10 rounded-lg bg-red-500 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-300"></i>
                </div>
            </div>
            @if($lastDownMonitor)
            <a href="{{route('edit-monitor', $lastDownMonitor->id)}}" class="text-3xl font-bold text-red-500">
                {{ $lastDownMonitor->name }}
            </a>
            @else
            <p class="text-3xl font-bold text-red-500">
                No recent downtime
            </p>
            @endif
            <p class="text-sm text-gray-500">Most recent downtime occurrence</p>
        </div>

        <!-- Up Monitors -->
        <div class="rounded-xl shadow-lg p-6 border-l-4 border-green-500 hover:-translate-y-1 transition-transform duration-300 bg-base-100">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Up Monitors</h2>
                <div class="w-10 h-10 rounded-lg bg-green-300 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-500"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-green-600">
                {{ max(0, $totalMonitors - $totalDownMonitors - $totalPausedMonitors) }}
            </p>
            <p class="text-sm text-gray-500">Currently operational systems</p>
        </div>

        <!-- Down Monitors -->
        <div class="rounded-xl shadow-lg p-6 border-l-4 border-red-500 hover:-translate-y-1 transition-transform duration-300 bg-base-100">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Down Monitors</h2>
                <div class="w-10 h-10 rounded-lg bg-red-300 flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-500"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-red-600">{{ $totalDownMonitors }}</p>
            <p class="text-sm text-gray-500">Systems requiring attention</p>
        </div>

        <!-- Paused Monitors -->
        <div class="rounded-xl shadow-lg p-6 border-l-4 border-yellow-500 hover:-translate-y-1 transition-transform duration-300 bg-base-100">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Paused Monitors</h2>
                <div class="w-10 h-10 rounded-lg bg-yellow-300 flex items-center justify-center">
                    <i class="fas fa-pause-circle text-yellow-500"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-yellow-600">{{ $totalPausedMonitors }}</p>
            <p class="text-sm text-gray-500">Temporarily disabled monitoring</p>
        </div>
    </div>

    <!-- Uptime and Downtime Summary -->
    <div class="mb-10 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="rounded-xl shadow-lg p-6 bg-base-100">
            <h3 class="text-xl font-semibold mb-2">Total Uptime</h3>
            <p class="text-3xl font-bold text-green-700">{{ number_format($totalUptime, 2) }} hours</p>
        </div>
        <div class="rounded-xl shadow-lg p-6 bg-base-100">
            <h3 class="text-xl font-semibold mb-2">Total Downtime</h3>
            <p class="text-3xl font-bold text-red-700">{{ number_format($totalDowntime, 2) }} hours</p>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="rounded-xl shadow-lg p-6 mb-10 bg-base-100">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6">
            <x-header title="Uptime Chart" subtitle="Visual representation of system uptime over time" />
            <div class="flex space-x-4 mt-4 md:mt-0">
                @foreach(['24 hours', '7 days', '30 days'] as $range)
                    <label class="inline-flex items-center space-x-2 cursor-pointer">
                        <input type="radio" wire:model="timeRange" value="{{ $range }}" class="form-radio text-green-600" />
                        <span class="text-sm text-gray-700">{{ $range }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <x-chart :chart="json_encode($myChart)" />
    </div>
</div>
