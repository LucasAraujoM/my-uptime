<?php

use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    public string $search = '';

    public bool $drawer = false;

    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    // Clear filters
    public function clear(): void
    {
        $this->reset();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    // Delete action
    public function delete($id): void
    {
        $this->warning("Will delete #$id", 'It is fake.', position: 'toast-bottom');
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'name', 'label' => 'Name', 'class' => 'w-64'],
            ['key' => 'age', 'label' => 'Role', 'class' => 'w-20'], // Renamed Age to Role for realism
            ['key' => 'email', 'label' => 'E-mail', 'sortable' => false],
        ];
    }

    public function users(): Collection
    {
        return collect([
            ['id' => 1, 'name' => 'Mary', 'email' => 'mary@example.com', 'age' => 'Admin'],
            ['id' => 2, 'name' => 'Giovanna', 'email' => 'giovanna@example.com', 'age' => 'Editor'],
            ['id' => 3, 'name' => 'Marina', 'email' => 'marina@example.com', 'age' => 'Viewer'],
        ])
            ->sortBy([[...array_values($this->sortBy)]])
            ->when($this->search, function (Collection $collection) {
                return $collection->filter(fn(array $item) => str($item['name'])->contains($this->search, true));
            });
    }

    public function with(): array
    {
        return [
            'users' => $this->users(),
            'headers' => $this->headers()
        ];
    }
}; ?>

<div>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Team Members</h1>
            <p class="text-gray-400 text-sm mt-1">Manage access and roles for your organization.</p>
        </div>
        <div class="flex items-center gap-2">
            <x-button label="Invite User" icon="o-plus"
                class="bg-purple-600 hover:bg-purple-700 text-white border-none shadow-lg shadow-purple-900/20" />
            <x-button icon="o-funnel" @click="$wire.drawer = true" class="btn-ghost text-gray-400 hover:text-white" />
        </div>
    </div>

    <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-2xl p-6">
        <!-- Search -->
        <div class="mb-6 w-full md:w-72">
            <x-input placeholder="Search users..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass"
                class="bg-gray-900/50 border-gray-700 text-white placeholder-gray-500 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" />
        </div>

        <!-- Table -->
        <div class="overflow-hidden rounded-xl border border-gray-700/50">
            <x-table :headers="$headers" :rows="$users" :sort-by="$sortBy" class="bg-gray-900/20 text-gray-300">
                @scope('cell_name', $user)
                <div class="flex items-center gap-3">
                    <x-avatar placeholder="{{ substr($user['name'], 0, 1) }}"
                        class="!w-8 !h-8 bg-gray-700 text-gray-200" />
                    <span class="font-medium text-white">{{ $user['name'] }}</span>
                </div>
                @endscope

                @scope('cell_age', $user)
                <span class="px-2 py-1 rounded-md bg-gray-800 text-xs font-medium text-gray-400 border border-gray-700">
                    {{ $user['age'] }}
                </span>
                @endscope

                @scope('actions', $user)
                <div class="flex justify-end">
                    <x-button icon="o-trash" wire:click="delete({{ $user['id'] }})" wire:confirm="Are you sure?" spinner
                        class="btn-ghost btn-sm text-gray-500 hover:text-red-400 hover:bg-red-500/10" />
                </div>
                @endscope
            </x-table>
        </div>
    </div>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button
        class="bg-gray-900 text-white lg:w-1/3">
        <div class="space-y-4 p-4">
            <x-input placeholder="Search..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                class="bg-gray-800 border-gray-700 text-white placeholder-gray-500" />

            <!-- Add more filters here if needed -->
        </div>

        <x-slot:actions>
            <div class="flex gap-2 w-full p-4">
                <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner
                    class="flex-1 btn-ghost text-gray-300 hover:text-white" />
                <x-button label="Done" icon="o-check"
                    class="flex-1 bg-purple-600 text-white border-none hover:bg-purple-700"
                    @click="$wire.drawer = false" />
            </div>
        </x-slot:actions>
    </x-drawer>
</div>