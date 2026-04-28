<?php

use Livewire\Volt\Component;
use App\Models\Monitor;
use Illuminate\Support\Facades\Auth;
use App\Models\Log;

new class extends Component {
    use Livewire\WithPagination;

    public function with(): array
    {
        return [
            'logs' => Log::whereIn('monitor_id', Monitor::where('user_id', Auth::id())->pluck('id'))
                ->where('status', 'down')
                ->with('logResponse')
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ];
    }
}; ?>

<div>
    <x-header title="Incidents" subtitle="History of service outages and degradations" size="lg" />

    <x-card>
        <x-slot:title>
            <div class="flex items-center justify-between">
                <span>All Incidents</span>
                <x-button label="Export CSV" icon="o-arrow-down-tray" class="btn-ghost text-gray-400 hover:text-white btn-sm" />
            </div>
        </x-slot:title>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center">Monitor</th>
                        <th class="text-center">Started At</th>
                        <th class="text-center">Cause</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr class="hover:bg-gray-800/50">
                            <td class="text-center text-white">{{ $log->monitor->name }}</td>
                            <td class="text-center text-gray-300">{{ $log->created_at }}</td>
                            <td class="text-center">
                                <button class="cursor-pointer hover:text-red-500 text-gray-300"
                                    onclick="my_modal_{{ $log->id }}.showModal()">
                                    {{ substr($log->error_message, 0, 50) }}
                                </button>
                                <dialog id="my_modal_{{ $log->id }}" class="modal">
                                    <div class="modal-box bg-gray-800 border border-gray-700">
                                        <h3 class="text-lg font-bold text-white">Response Content</h3>
                                        <p class="py-4 text-gray-300">{{ $log->error_message }}</p>
                                        <div class="modal-action">
                                            <form method="dialog">
                                                <button class="btn btn-ghost text-white">Close</button>
                                            </form>
                                        </div>
                                    </div>
                                </dialog>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <x-slot:actions>
            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        </x-slot:actions>
    </x-card>
</div>
</div>