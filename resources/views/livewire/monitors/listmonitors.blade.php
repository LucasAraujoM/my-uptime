<?php

use App\Models\Monitor;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $drawer = false;
    public array $sortBy = ['column' => 'status', 'direction' => 'asc'];
    public bool $myModal1 = false;
    public $monitorToDelete = '';
    public $search = '';
    public $statusFilters = [];

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilters = [];
    }

    public function hasActiveFilters()
    {
        return !empty($this->search) ||
            !empty($this->statusFilters);
    }

    public function activeFilterCount()
    {
        $count = 0;

        if (!empty($this->search)) $count++;
        if (!empty($this->statusFilters)) $count++;

        return $count;
    }

    public function monitors()
    {
        $query = Monitor::query();
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('url', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->statusFilters)) {
            $query->whereIn('status', $this->statusFilters);
        }

        return $query->orderBy(...array_values($this->sortBy))->paginate(10);
    }
    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => 'id', 'class' => 'hidden'],
            ['key' => 'name', 'label' => 'Name', 'class' => 'w-35', 'sortable' => false],
            ['key' => 'url', 'label' => 'URL', 'class' => 'w-20', 'sortable' => false],
            ['key' => 'status', 'label' => 'Status', 'sortable' => true],
        ];
    }
    public function with(): array
    {
        return [
            'monitors' => $this->monitors(),
            'headers' => $this->headers()
        ];
    }
    public function delete($monitor)
    {
        $this->monitorToDelete = $monitor;
        $this->myModal1 = true;
        try {
            $monitor->delete();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}; ?>

<div>
    <x-header title="Monitors" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Reset Filters" icon="o-x-mark" wire:click="resetFilters" class="mr-2" :disabled="!$this->hasActiveFilters()" />
            <x-dropdown label="Filters" icon="o-funnel" :class="$this->hasActiveFilters() ? 'text-primary' : ''">
                @if($this->activeFilterCount() > 0)
                <x-badge value="{{ $this->activeFilterCount() }}" class="absolute -top-1 -right-1" />
                @endif
                <x-menu-item @click.stop="">
                    <x-checkbox label="Up" wire:model.live.debounce="statusFilters" value="up" />
                </x-menu-item>
                <x-menu-item @click.stop="">
                    <x-checkbox label="Down" wire:model.live.debounce="statusFilters" value="down" />
                </x-menu-item>
                <x-menu-item @click.stop="">
                    <x-checkbox label="Paused" wire:model.live.debounce="statusFilters" value="paused" />
                </x-menu-item>
                <x-menu-item @click.stop="">
                    <x-checkbox label="Pending" wire:model.live.debounce="statusFilters" value="pending" />
                </x-menu-item>
            </x-dropdown>
        </x-slot:actions>
    </x-header>
    @if($this->hasActiveFilters())
    <div class="bg-base-200 p-2 rounded-lg mb-4 flex flex-wrap items-center">
        <span class="mr-2 font-medium">Active filters:</span>
        @if(!empty($this->statusFilters))
        @foreach($this->statusFilters as $status)
        <span class="mr-2 px-2 py-1 rounded-md bg-base-300 text-sm">
            {{ ucfirst($status) }}
        </span>
        @endforeach
        @endif
        <div class="flex-grow"></div>
        <div class="flex items-center">
            <span class="text-sm mr-4">{{ $monitors->total() }} result(s) found</span>
            <x-button icon="o-x-mark" size="xs" wire:click="resetFilters()" label="Clear all" />
        </div>
    </div>
    @endif
    <x-card shadow>
        <x-table :headers="$headers" :rows="$monitors" :sort-by="$sortBy" with-pagination>
            @scope('actions', $monitor)
            <div class="flex">
                <x-modal @close="$wire.delete($monitor)" wire:model="myModal1" title="Are you sure?" class="backdrop-blur text-start">
                    This change will permanently delete {{$monitor->name}}.
                    <x-slot:actions>
                        <x-button label="Cancel" @click="$wire.myModal1 = false" />
                        <x-button label="Confirm" @click="$wire.delete($monitor)" />
                    </x-slot:actions>
                </x-modal>
                <x-button icon="o-trash" spinner class="btn-ghost btn-sm text-error" @click="$wire.myModal1 = true; $wire.monitorToDelete = $monitor" />
            </div>
            @endscope
        </x-table>
    </x-card>
</div>