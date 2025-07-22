<?php

use Illuminate\Support\Arr;
use Livewire\Volt\Component;

new class extends Component {
    public array $myChart = [
        'type' => 'line',
        'data' => [
            'labels' => ['Mary', 'Joe', 'Ana'],
            'datasets' => [
                [
                    'label' => '# of Votes',
                    'data' => [12, 19, 3],
                ]
            ]
        ]
    ];
    public function randomize()
    {
        Arr::set($this->myChart, 'data.datasets.0.data', [fake()->randomNumber(2), fake()->randomNumber(2), fake()->randomNumber(2)]);
    }
    public function switch()
    {
        $type = $this->myChart['type'] == 'bar' ? 'pie' : 'bar';
        Arr::set($this->myChart, 'type', $type);
    }
}; ?>

<div class="grid grid-cols-6 grid-rows-7 gap-4">
    <x-stat class="col-span-2 row-span-2"
        title="Messages"
        value="44"
        icon="o-envelope"
        tooltip="Hello"
        color="text-primary" />

    <x-stat class="col-span-2 row-span-2 col-start-3"
        title="Sales"
        description="This month"
        value="22.124"
        icon="o-arrow-trending-up"
        tooltip-bottom="There" />

    <x-stat class="col-span-2 row-span-2 col-start-5"
        title="Lost"
        description="This month"
        value="34"
        icon="o-arrow-trending-down"
        tooltip-left="Ops!" />

    <x-stat class="col-span-2 row-span-2 row-start-3 text-orange-500"
        title="Sales"
        description="This month"
        value="22.124"
        icon="o-arrow-trending-down"
        color="text-pink-500"
        tooltip-right="Gosh!" />
    <x-stat class="col-span-2 row-span-2 col-start-3 row-start-3"
        title="Lost"
        description="This month"
        value="34"
        icon="o-arrow-trending-down"
        tooltip-left="Ops!" />
    <x-stat class="col-span-2 row-span-2 col-start-5 row-start-3"
        title="Lost"
        description="This month"
        value="34"
        icon="o-arrow-trending-down"
        tooltip-left="Ops!" />
    <div class="col-span-6 row-span-3 row-start-5">
        <x-button label="Randomize" wire:click="randomize" class="btn-primary" spinner />
        <x-button label="Switch" wire:click="switch" spinner />

    </div>
    <x-chart wire:model="myChart" />

</div>