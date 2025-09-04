<?php

use App\Models\Monitor;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public $userName;

    public function mount()
    {
        $this->userName = Auth::user()->name;
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
    <x-header title="Monitors - {{ $userName }}" separator progress-indicator>
    </x-header>
    <x-card shadow>
        <div class="grid grid-cols-2 gap-4">
            <x-chart wire:model="myChart" />
        </div>
        <x-button wire:click="switch">Switch to {{ $myChart['type'] == 'bar' ? 'Pie' : 'Bar' }}</x-button>
        <x-button wire:click="uptime">Uptime</x-button>
    </x-card>
</div>