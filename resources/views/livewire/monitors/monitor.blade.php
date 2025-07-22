<?php

use App\Models\Monitor;
use Livewire\Volt\Component;

new class extends Component {

    public string $name = '';
    public string $url = '';
    public string $condition = '';
    public string $interval = '';
    public string $method = '';
    public string $keyword = '';
    public string $type = ';';
    public $methods = [
        ["id" => 0, "name" => "GET"],
        ["id" => 1, "name" => "POST"],
        ["id" => 2, "name" => "PUT"],
        ["id" => 3, "name" => "DELETE"],
    ];
    public $intervals = [
        ["id" => 0, "name" => "every 60 seconds"],
        ["id" => 1, "name" => "every 10 minutes"],
        ["id" => 2, "name" => "every 30 minutes"],
        ["id" => 3, "name" => "every 1 hour"],
        ["id" => 4, "name" => "every 2 hours"],
        ["id" => 5, "name" => "every 3 hours"],
        ["id" => 6, "name" => "every 4 hours"],
        ["id" => 7, "name" => "every 5 hours"],
        ["id" => 8, "name" => "every 6 hours"],
        ["id" => 9, "name" => "every 12 hours"],
        ["id" => 10, "name" => "every 24 hours"],
    ];
    public $conditions = [
        ["id" => 0, "name" => "No Keyword Monitoring"],
        ["id" => 1, "name" => "When Keyword exists"],
        ["id" => 2, "name" => "When Keyword not exists"],
    ];
    public $timeouts = [
        ["id" => 0, "name" => "10 seconds"],
        ["id" => 1, "name" => "30 seconds"],
        ["id" => 2, "name" => "1 minute"],
        ["id" => 3, "name" => "5 minutes"],
    ];

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'url' => 'required|url',
            'type' => 'required',
            'keyword' => 'required',
            'interval' => 'required'
        ]);
        try {
            $monitor = new Monitor();
            $monitor->name = $this->name;
            $monitor->url = $this->url;
            //$monitor->method = $this->method;
            //$monitor->condition = $this->condition;
            //$monitor->interval = $this->interval;
            $monitor->status = 'down';
            $monitor->type = $this->type;
            $monitor->keyword = $this->keyword;
            $monitor->save();
            return redirect('/');
        } catch (Exception $e) {
            $this->addError('url', $e->getMessage());
        }
    }
    public function redirectBack()
    {
        return redirect()->back();
    }
}; ?>

<div>
    <x-header title="Create Monitor" separator progress-indicator>
    </x-header>

    <x-form wire:submit="save" no-separator class="grid grid-cols-2 gap-4">
        <x-input label="Name" wire:model="name" placeholder="Endpoint1" />
        <x-input label="URL" wire:model="url" placeholder="www.example.com/endpoint1" />
        <x-select
            label="Method"
            wire:model="method"
            :options="$methods"
            placeholder="Select a method" />
        <x-select
            label="Interval"
            wire:model="interval"
            :options="$intervals"
            placeholder="Select an interval" />
        <x-select
            label="Condition"
            wire:model="condition"
            :options="$conditions" />
        <x-input label="Keyword to check" wire:model="keyword" />
        <x-select
            label="Timeout"
            wire:model="timeout"
            :options="$timeouts" />
        <x-slot:actions>
            <x-button label="Create Monitor" class="btn-primary" type="submit" spinner="save" />
            <x-button label="Cancel" class="btn-secondary" wire:click="redirectBack" />
        </x-slot:actions>
    </x-form>
</div>