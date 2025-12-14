<?php

use Livewire\Volt\Component;

new class extends Component {
    public $email = true;
    public $slack = false;
    public $discord = false;
    public $webhook = '';

    public function save()
    {
        // Placeholder save logic
        session()->flash('success', 'Alert settings updated!');
    }
}; ?>

<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white tracking-tight">Alert Settings</h1>
        <p class="text-gray-400 text-sm mt-1">Configure how and when you want to be notified.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Channels -->
        <div class="space-y-6">
            <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-2xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                    <x-icon name="o-bell" class="w-5 h-5 text-purple-400" />
                    Notification Channels
                </h2>

                <div class="space-y-4">
                    <div
                        class="flex items-center justify-between p-4 rounded-xl bg-gray-900/40 border border-gray-700/50">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-500/10 rounded-lg">
                                <x-icon name="o-envelope" class="w-5 h-5 text-blue-400" />
                            </div>
                            <div>
                                <div class="font-medium text-white">Email Notifications</div>
                                <div class="text-xs text-gray-500">Receive alerts via email</div>
                            </div>
                        </div>
                        <x-toggle wire:model="email" class="toggle-success" />
                    </div>

                    <div
                        class="flex items-center justify-between p-4 rounded-xl bg-gray-900/40 border border-gray-700/50 opacity-75">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-[#4A154B]/30 rounded-lg">
                                <x-icon name="o-hashtag" class="w-5 h-5 text-white" />
                            </div>
                            <div>
                                <div class="font-medium text-white">Slack (Coming Soon)</div>
                                <div class="text-xs text-gray-500">Send notifications to channel</div>
                            </div>
                        </div>
                        <x-toggle wire:model="slack" disabled />
                    </div>

                    <div
                        class="flex items-center justify-between p-4 rounded-xl bg-gray-900/40 border border-gray-700/50 opacity-75">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-[#5865F2]/20 rounded-lg">
                                <x-icon name="o-chat-bubble-left-ellipsis" class="w-5 h-5 text-[#5865F2]" />
                            </div>
                            <div>
                                <div class="font-medium text-white">Discord (Coming Soon)</div>
                                <div class="text-xs text-gray-500">Send alerts to webhook</div>
                            </div>
                        </div>
                        <x-toggle wire:model="discord" disabled />
                    </div>
                </div>
            </div>
        </div>

        <!-- Global Rules -->
        <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-2xl p-6 h-fit">
            <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <x-icon name="o-adjustments-horizontal" class="w-5 h-5 text-purple-400" />
                Global Rules
            </h2>
            <p class="text-sm text-gray-400 mb-6">
                Configure when system-wide alerts should be triggered. You can override these per monitor.
            </p>

            <div class="space-y-4">
                <x-range min="1" max="5" step="1" wire:model="" label="Retry Count: 3"
                    hint="Number of failures before alerting" class="range-primary" disabled />

                <div class="pt-4">
                    <x-button label="Save Changes" wire:click="save"
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white border-none" />
                </div>
            </div>
        </div>
    </div>
</div>