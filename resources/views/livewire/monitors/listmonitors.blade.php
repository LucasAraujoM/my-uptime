<?php

use App\Models\Monitor;
use Livewire\Volt\Component;
use Illuminate\Support\Collection;

new class extends Component {
    public $search = '';
    public $drawer = false;
    public array $sortBy = ['column' => 'status', 'direction' => 'asc'];
    public bool $myModal1 = false;
    public $monitorToDelete = '';
    public function monitors(): Collection
    {
        /* return collect([
            ['id' => 1, 'name' => 'Mary', 'email' => 'mary@mary-ui.com', 'age' => 23],
            ['id' => 2, 'name' => 'Giovanna', 'email' => 'giovanna@mary-ui.com', 'age' => 7],
            ['id' => 3, 'name' => 'Marina', 'email' => 'marina@mary-ui.com', 'age' => 5],
        ])
            ->sortBy([[...array_values($this->sortBy)]])
            ->when($this->search, function (Collection $collection) {
                return $collection->filter(fn(array $item) => str($item['name'])->contains($this->search, true));
            }); */
        return Monitor::all();
    }
    public function headers(): array
    {
        return [
            /* ['key' => 'id', 'label' => '#', 'class' => 'w-1'], */
            ['key' => 'name', 'label' => 'Name', 'class' => 'w-35'],
            ['key' => 'url', 'label' => 'url', 'class' => 'w-20'],
            ['key' => 'status', 'label' => 'status', 'sortable' => true],
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
        dd($this->monitorToDelete);
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
    <x-modal @close="$wire.delete($monitor)" wire:model="myModal1" title="Hey" class="backdrop-blur">
        Press `ESC`, click outside or click `CANCEL` to close.

        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.myModal1 = false" />
            <x-button label="Confirm"  @open="$wire.delete()" />
        </x-slot:actions>
    </x-modal>
    <x-header title="Monitors" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel" />
        </x-slot:actions>
    </x-header>
    <x-card shadow>
        <x-table :headers="$headers" :rows="$monitors" :sort-by="$sortBy">
            @scope('actions', $monitor)
            <div class="flex">
                <x-button icon="o-pencil" class="btn-ghost btn-sm" />
                <x-button icon="o-trash" spinner class="btn-ghost btn-sm text-error" @click="$wire.myModal1 = true" @click="$wire.monitorToDelete = $monitor" />
            </div>
            @endscope
        </x-table>
    </x-card>
</div>