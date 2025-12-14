<?php

use App\Models\Monitor;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $polling = true;
    public $drawer = false;
    public array $sortBy = ['column' => 'status', 'direction' => 'asc'];
    public bool $deleteModal = false;
    public Monitor $monitorToDelete;
    public $m_name = '';
    public $search = '';
    public $statusFilters = [];
    public array $selected = [];

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

        if (!empty($this->search))
            $count++;
        if (!empty($this->statusFilters))
            $count++;

        return $count;
    }

    public function monitors()
    {
        $query = Monitor::query()->where('user_id', Auth::user()->id);
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('url', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->statusFilters)) {
            $query->whereIn('status', $this->statusFilters);
        }

        return $query->orderBy(...array_values($this->sortBy))->paginate(15);
    }
    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => 'id', 'class' => 'hidden'],
            ['key' => 'name', 'label' => 'Name', 'class' => 'w-70', 'sortable' => false],
            ['key' => 'url', 'label' => 'URL', 'class' => 'w-150', 'sortable' => false],
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
    public function confirmDelete(Monitor $monitor)
    {
        $this->monitorToDelete = $monitor;
        $this->m_name = $monitor->name;
        $this->deleteModal = true;
    }
    public function delete()
    {
        $this->monitorToDelete->delete();
        $this->deleteModal = false;
    }
    public function edit($monitorId)
    {
        $this->redirectRoute('edit-monitor', $monitorId);
    }
}; ?>

<div>
    <x-modal wire:model="deleteModal" title="Confirm Deletion"
        class="backdrop-blur-md bg-gray-900/90 border border-gray-800">
        <div class="text-gray-300">
            Are you sure you want to permanently delete the monitor <span
                class="font-bold text-white">{{$m_name}}</span>?
            <p class="text-sm text-gray-500 mt-2">This action cannot be undone and all historical logs will be lost.</p>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.deleteModal = false"
                class="btn-ghost text-gray-400 hover:text-white" />
            <x-button label="Delete Monitor" @click="$wire.delete()"
                class="bg-red-500 hover:bg-red-600 border-none text-white" />
        </x-slot:actions>
    </x-modal>

    <!-- Header & Controls -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Monitors</h1>
            <p class="text-gray-400 text-sm mt-1">Manage and track your services.</p>
        </div>
        <div class="flex items-center gap-2">
            <x-button label="Add Monitor" link="{{ route('add-monitor') }}" icon="o-plus"
                class="bg-purple-600 hover:bg-purple-700 text-white border-none shadow-lg shadow-purple-900/20" />
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-2xl p-6">

        <!-- Toolbar -->
        <div class="flex flex-col md:flex-row gap-4 mb-6 justify-between items-center">
            <div class="w-full md:w-72">
                <x-input placeholder="Search monitors..." wire:model.live.debounce="search" clearable
                    icon="o-magnifying-glass"
                    class="bg-gray-900/50 border-gray-700 text-white placeholder-gray-500 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" />
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto relative z-20">
                <x-dropdown label="Filter Status" icon="o-funnel" right
                    class="btn-ghost bg-gray-900/30 border border-gray-700/50 text-gray-300 hover:text-white hover:bg-gray-800 {{ $this->hasActiveFilters() ? 'text-purple-400 border-purple-500/30' : '' }}">

                    @if($this->activeFilterCount() > 0)
                        <x-badge value="{{ $this->activeFilterCount() }}"
                            class="absolute -top-1 -right-1 bg-purple-500 text-white border-none scale-75" />
                    @endif

                    <div class="p-3 bg-gray-800 border border-gray-700 shadow-xl rounded-xl space-y-2 min-w-[200px]">
                        <x-checkbox label="Operational (Up)" wire:model.live="statusFilters" value="up"
                            class="checkbox-success" />
                        <x-checkbox label="Downtime (Down)" wire:model.live="statusFilters" value="down"
                            class="checkbox-error" />
                        <x-checkbox label="Paused" wire:model.live="statusFilters" value="paused"
                            class="checkbox-warning" />
                        <x-checkbox label="Pending" wire:model.live="statusFilters" value="pending"
                            class="checkbox-info" />
                    </div>
                </x-dropdown>

                @if($this->hasActiveFilters())
                    <x-button icon="o-x-mark" wire:click="resetFilters" class="btn-ghost text-gray-400 hover:text-white"
                        tooltip="Clear Filters" />
                @endif
            </div>
        </div>

        <!-- Active Filters Display -->
        @if($this->hasActiveFilters())
            <div class="flex flex-wrap gap-2 mb-4 items-center animate-fade-in">
                <span class="text-xs font-semibold text-gray-500 uppercase mr-1">Filtering by:</span>
                @foreach($this->statusFilters as $status)
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-700 text-gray-200 border border-gray-600">
                        {{ ucfirst($status) }}
                    </span>
                @endforeach
                @if($search)
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-700 text-gray-200 border border-gray-600">
                        Search: "{{ $search }}"
                    </span>
                @endif
            </div>
        @endif

        <!-- Table -->
        <div class="overflow-hidden rounded-xl border border-gray-700/50" wire:poll.30s>
            <x-table :headers="$headers" :rows="$monitors" :sort-by="$sortBy" with-pagination wire:poll.5s
                class="bg-gray-900/20 text-gray-300">

                @scope('cell_name', $monitor)
                <div class="flex items-center gap-3 py-1">
                    <div
                        class="w-8 h-8 rounded-lg flex items-center justify-center bg-gray-800 border border-gray-700 shrink-0">
                        @if($monitor->type === 'ping')
                            <x-icon name="o-signal" class="w-4 h-4 text-orange-400" />
                        @else
                            <x-icon name="o-globe-alt" class="w-4 h-4 text-blue-400" />
                        @endif
                    </div>
                    <div>
                        <div class="font-medium text-white hover:text-purple-400 transition-colors cursor-pointer"
                            wire:click="edit({{$monitor->id}})">
                            {{ $monitor->name }}
                        </div>
                        <div class="text-xs text-gray-500">{{ strtoupper($monitor->type) }} • {{ $monitor->interval }}
                        </div>
                    </div>
                </div>
                @endscope

                @scope('cell_url', $monitor)
                <div class="flex items-center group">
                    <x-icon name="o-arrow-top-right-on-square"
                        class="w-3 h-3 text-gray-600 mr-2 group-hover:text-purple-400 transition-colors" />
                    <a href="{{ $monitor->url }}" target="_blank"
                        class="text-gray-400 hover:text-purple-400 hover:underline transition-colors truncate max-w-[200px] text-sm font-mono">
                        {{ $monitor->url }}
                    </a>
                </div>
                @endscope

                @scope('cell_status', $monitor)
                @php
                    $color = match ($monitor->status) {
                        'up' => 'success',
                        'down' => 'error',
                        'paused' => 'warning',
                        default => 'info'
                    };
                    $bg = match ($monitor->status) {
                        'up' => 'bg-green-500/10 text-green-400 border-green-500/20',
                        'down' => 'bg-red-500/10 text-red-400 border-red-500/20',
                        'paused' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
                        default => 'bg-blue-500/10 text-blue-400 border-blue-500/20'
                    };
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $bg }}">
                    <span
                        class="w-1.5 h-1.5 rounded-full bg-current mr-1.5 {{ $monitor->status === 'down' ? 'animate-pulse' : '' }}"></span>
                    {{ ucfirst($monitor->status) }}
                </span>
                @endscope

                @scope('actions', $monitor, $m_name)
                <div class="flex justify-end gap-1">
                    <x-button icon="o-pencil"
                        class="btn-ghost btn-sm text-gray-500 hover:text-blue-400 hover:bg-blue-500/10"
                        wire:click="edit({{$monitor->id}})" />
                    <x-button icon="o-trash" wire:click="confirmDelete({{ $monitor->id }})"
                        class="btn-ghost btn-sm text-gray-500 hover:text-red-400 hover:bg-red-500/10" />
                </div>
                @endscope
            </x-table>
        </div>
    </div>
</div>