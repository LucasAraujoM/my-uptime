<?php

use App\Models\Monitor;
use Livewire\Volt\Component;

new class extends Component {

    public ?int $id = null;
    public string $name = '';
    public string $url = '';
    public string $keyword = '';
    public string $type = 'http';
    public string $method = '';
    public string $interval = '';
    public string $condition = '';
    public string $timeout = '';

    public array $methods;
    public array $intervals;
    public array $conditions;
    public array $timeouts;
    public array $types = [
        ['id' => 'http', 'name' => 'HTTP'],
        ['id' => 'ping', 'name' => 'Ping']
    ];

    public function mount($id = null)
    {
        $this->methods = config('constants.methods');
        $this->intervals = config('constants.intervals');
        $this->conditions = config('constants.conditions');
        $this->timeouts = config('constants.timeouts');
        
        if ($id) {
            $this->id = $id;
            $monitor = Monitor::findOrFail($id);
            $this->name = $monitor->name;
            $this->url = $monitor->url;
            $this->type = $monitor->type;
            $this->keyword = $monitor->keyword ?? '';
            $this->method = $monitor->method ?? '';
            $this->interval = $monitor->interval;
            $this->condition = $monitor->condition ?? '';
            $this->timeout = $monitor->timeout ?? '';
        }
    }

    public function save()
    {
        $validationRules = [
            'name' => 'required',
            'url' => 'required|url',
            'type' => 'required|in:http,ping',
            'interval' => 'required'
        ];

        // Only require keyword if condition is set to check for keyword
        if ($this->condition == '1' || $this->condition == '2') {
            $validationRules['keyword'] = 'required';
        }

        $this->validate($validationRules);

        try {
            if (isset($this->id)) {
                $monitor = Monitor::findOrFail($this->id);
            } else {
                $monitor = new Monitor();
                $monitor->status = 'pending';
            }
            
            $monitor->name = $this->name;
            $monitor->url = $this->url;
            $monitor->method = $this->method;
            $monitor->condition = $this->condition;
            $monitor->interval = $this->interval;
            $monitor->type = $this->type;
            $monitor->keyword = $this->keyword;
            $monitor->timeout = $this->timeout;
            $monitor->save();
            
            $message = isset($this->id) ? 'Monitor updated successfully!' : 'Monitor created successfully!';
            session()->flash('message', $message);
            return redirect('/monitors');
        } catch (\Exception $e) {
            $this->addError('url', $e->getMessage());
        }
    }
    
    public function redirectBack()
    {
        return redirect('/monitors');
    }
}; ?>

<div>
    <x-header title="{{ $id ? 'Edit' : 'Create' }} Monitor" separator progress-indicator>
    </x-header>

    <x-form wire:submit="save" no-separator class="grid grid-cols-2 gap-4">
        <x-input label="Name" wire:model="name" placeholder="Endpoint1" />
        <x-input label="URL" wire:model="url" placeholder="www.example.com/endpoint1" />
        <x-select
            label="Type"
            wire:model="type"
            :options="$types"
            option-label="name"
            option-value="id" />
        <x-select
            label="Method"
            wire:model="method"
            :options="$methods"
            placeholder="Select a method"
            x-show="$wire.type === 'http'" />
        <x-select
            label="Interval"
            wire:model="interval"
            :options="$intervals"
            placeholder="Select an interval" />
        <x-select
            label="Condition"
            wire:model="condition"
            :options="$conditions"
            x-show="$wire.type === 'http'" />
        <x-input 
            label="Keyword to check" 
            wire:model="keyword" 
            x-show="$wire.condition == '1' || $wire.condition == '2'" 
            help-text="The monitor will check if this keyword is present in the response." />
        <x-select
            label="Timeout"
            wire:model="timeout"
            :options="$timeouts" />
        <x-slot:actions>
            <x-button label="{{ $id ? 'Update' : 'Create' }} Monitor" class="btn-primary" type="submit" spinner="save" />
            <x-button label="Cancel" class="btn-secondary" wire:click="redirectBack" />
        </x-slot:actions>
    </x-form>
</div>