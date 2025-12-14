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
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Incidents</h1>
            <p class="text-gray-400 text-sm mt-1">History of service outages and degradations.</p>
        </div>
        <div class="flex items-center gap-2">
            <x-button label="Export CSV" icon="o-arrow-down-tray" class="btn-ghost text-gray-400 hover:text-white" />
        </div>
    </div>

    <!-- Empty State -->
    <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-2xl p-12 text-center">
        <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
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
                        <tr>
                            <td class="text-center">{{ $log->monitor->name }}</td>
                            <td class="text-center">{{ $log->created_at }}</td>
                            <td class="text-center"><button class="cursor-pointer hover:text-red-500"
                                    onclick="my_modal_{{ $log->id }}.showModal()">{{ substr($log->error_message, 0, 50) }}</button>
                                <dialog id="my_modal_{{ $log->id }}" class="modal">
                                    <div class="modal-box">
                                        <h3 class="text-lg font-bold">Response Content</h3>
                                        <p class="py-4">{{ $log->error_message }}</p>
                                        <div class="modal-action">
                                            <form method="dialog">
                                                <button class="btn">Close</button>
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
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</div>
</div>