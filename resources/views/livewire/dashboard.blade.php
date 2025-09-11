<?php

use App\Models\Monitor;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public $userName;
    public $lastDownMonitor;
    public $totalMonitors;
    public $totalUptime;
    public $totalDowntime;

    public function mount()
    {
        $this->userName = Auth::user()->name;
        $this->lastDownMonitor = Monitor::where('user_id', Auth::user()->id)->where('status', 0)->latest()->first();
        $this->totalMonitors = Monitor::where('user_id', Auth::user()->id)->count();
        $this->totalUptime = User::find(Auth::user()->id)->uptimes()->sum('uptime_12h');
        $this->totalDowntime = User::find(Auth::user()->id)->uptimes()->sum('downtime_12h');
    }
    public array $myChart = [
        'type' => 'line',
        'data' => [
            'labels' => [],
            'datasets' => [
                [
                    'label' => '# of Votes',
                    'data' => [],
                ]
            ]
        ]
    ];
    public function uptime()
    {
        $uptimes = User::find(Auth::user()->id)->uptimes();
        Arr::set($this->myChart, 'data.datasets.0.data', $uptimes->pluck('uptime_12h')->toArray());
        Arr::set($this->myChart, 'data.labels', $uptimes->pluck('created_at')->toArray());
    }
    public function switch()
    {
        $type = $this->myChart['type'] == 'bar' ? 'pie' : 'bar';
        Arr::set($this->myChart, 'type', $type);
    }
}; ?>

<div>
    @section('title', 'Dashboard')
    @include('components.flash.messages')
    <x-header title="Monitors - {{ $userName }}" separator progress-indicator>
    </x-header>
    <div class="flex justify-between items-center">
        <x-mary-card title="Total Monitors:" :value="$totalMonitors" icon="o-squares-plus" />
        <x-mary-card title="Total Uptime:" :value="$totalUptime . ' hours'" icon="o-squares-plus" />
        <x-mary-card title="Total Downtime:" :value="$totalDowntime . ' hours'" icon="o-squares-plus" />
    </div>
</div>