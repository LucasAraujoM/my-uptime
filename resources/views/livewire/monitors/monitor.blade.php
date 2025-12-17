<?php

use App\Models\Log;
use App\Models\Monitor;
use App\Models\Header;
use App\Models\Parameter;
use App\Models\Body;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Jobs\CheckMonitor;

new class extends Component {
    use WithPagination;

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
    public array $headers = [];
    public array $parameters = [];
    public array $body = [];
    public array $chartLabels = [];
    public array $chartData = [];
    public Monitor $monitor;

    public function mount($id = null)
    {
        $this->methods = config('constants.methods');
        $this->intervals = config('constants.intervals');
        $this->conditions = config('constants.conditions');
        $this->timeouts = config('constants.timeouts');
        if ($id) {
            $this->monitor = Monitor::find($id);
            $this->id = $id;
            $this->name = $this->monitor->name;
            $this->url = $this->monitor->url;
            $this->type = $this->monitor->type;
            $this->keyword = $this->monitor->keyword ?? '';
            $this->method = $this->monitor->method ?? '';
            $this->interval = $this->monitor->interval;
            $this->condition = $this->monitor->condition ?? '';
            $this->timeout = $this->monitor->timeout ?? '';
            $this->headers = $this->monitor->headers()->get()->toArray();
            $this->parameters = $this->monitor->parameters()->get()->toArray();
            $this->body = $this->monitor->body()->get()->toArray();
            $this->loadChartData();
        }
    }

    public function loadChartData()
    {
        $logs = Log::where('monitor_id', $this->id)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->sortBy('created_at');

        $this->chartLabels = $logs->map(fn($log) => $log->created_at->format('H:i:s'))->values()->toArray();
        $this->chartData = $logs->map(fn($log) => $log->response_time)->values()->toArray();
    }

    public function logs()
    {
        $query = Log::where('monitor_id', $this->id)->orderBy('created_at', 'desc')->paginate(10);
        return $query;
    }
    public function logHeaders()
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'hidden'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'response_time', 'label' => 'Response Time'],
            ['key' => 'created_at', 'label' => 'Time'],
        ];
    }
    public function with(): array
    {
        return [
            'logs' => $this->logs(),
            'logHeaders' => $this->logHeaders()
        ];
    }
    public function save()
    {
        $validationRules = [
            'name' => 'required',
            'url' => 'required|url',
            'type' => 'required|in:http,ping',
            'interval' => 'required',
            'body.*.body' => 'nullable|json',
        ];

        $messages = [
            'body.*.body.json' => 'The body field must be a valid JSON string.',
        ];

        // Only require keyword if condition is set to check for keyword
        if ($this->condition == '1' || $this->condition == '2') {
            $validationRules['keyword'] = 'required';
        }

        $this->validate($validationRules, $messages);

        try {
            if (isset($this->id)) {
                $monitor = Monitor::findOrFail($this->id);
            } else {
                $monitor = new Monitor();
                $monitor->status = 'pending';
            }
            $monitor->user_id = Auth::user()->id;
            $monitor->name = $this->name;
            $monitor->url = $this->url;
            $monitor->method = $this->method;
            $monitor->condition = $this->condition;
            $monitor->interval = $this->interval;
            $monitor->type = $this->type;
            $monitor->keyword = $this->keyword;
            $monitor->timeout = $this->timeout;
            $monitor->save();
            if (!empty($this->headers)) {
                foreach ($this->headers as $key => $value) {
                    if (empty($value['key']) || empty($value['value'])) {
                        continue;
                    }
                    Header::updateOrCreate([
                        'id' => $value['id'] ?? null,
                        'monitor_id' => $monitor->id,
                        'key' => $value['key'],
                        'value' => $value['value']
                    ]);
                }
            } else {
                Header::where('monitor_id', $monitor->id)->delete();
            }
            if (!empty($this->parameters)) {
                foreach ($this->parameters as $key => $value) {
                    if (empty($value['key']) || empty($value['value'])) {
                        continue;
                    }
                    Parameter::updateOrCreate([
                        'id' => $value['id'] ?? null,
                        'monitor_id' => $monitor->id,
                        'key' => $value['key'],
                        'value' => $value['value']
                    ]);
                }
            } else {
                Parameter::where('monitor_id', $monitor->id)->delete();
            }
            if (!empty($this->body)) {
                foreach ($this->body as $key => $value) {
                    if (empty($value['body'])) {
                        Body::where('monitor_id', $monitor->id)->delete();
                        continue;
                    }
                    Body::updateOrCreate([
                        'id' => $value['id'] ?? null,
                        'monitor_id' => $monitor->id,
                        'body' => $value['body'] ?? ''
                    ]);
                }
            }
            CheckMonitor::dispatch($monitor->id);
            $message = isset($this->id) ? 'Monitor updated successfully!' : 'Monitor created successfully!';
            session()->flash('message', $message);
        } catch (\Exception $e) {
            $this->addError('url', $e->getMessage());
        }
    }

    public function pause()
    {
        $this->monitor->pause();
    }
    public function redirectBack()
    {
        return redirect('/monitors');
    }
    public function addHeader()
    {
        $this->headers[] = ['key' => '', 'value' => ''];
    }
    public function removeHeader($index)
    {
        unset($this->headers[$index]);
        $this->headers = array_values($this->headers);
    }
    public function addParameter()
    {
        $this->parameters[] = ['key' => '', 'value' => ''];
    }
    public function removeParameter($index)
    {
        unset($this->parameters[$index]);
        $this->parameters = array_values($this->parameters);
    }
    public function addBody()
    {
        $this->body[] = ['body' => ''];
    }
    public function removeBody($index)
    {
        unset($this->body[$index]);
        $this->body = array_values($this->body);
    }
}; ?>

<div>
    @if(session()->has('message'))
        <div role="alert" class="alert alert-success" wire:poll.5s
            style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">{{ $id ? 'Edit' : 'Create' }} Monitor</h1>
            <p class="text-gray-400 text-sm mt-1">Configure your endpoint monitoring settings.</p>
        </div>
        <div class="flex items-center gap-2">
            <x-button label="Back to Monitors" link="{{ route('monitors') }}" icon="o-arrow-left"
                class="btn-ghost text-gray-400 hover:text-white" />
        </div>
    </div>

    <!-- Status Overview (Only on Edit) -->
    @if($id)
        <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-2xl p-6 mb-8">
            <h2 class="text-lg font-semibold text-white mb-4">Uptime History</h2>
            <!-- Visual Status Bar -->
            <div class="w-full h-8 flex overflow-hidden rounded-lg border border-gray-700/50 bg-gray-900/50" wire:poll.10s>
                @foreach($logs as $log)
                    @php
                        $colorClass = match ($log->status) {
                            'up' => 'bg-green-500 hover:bg-green-400 border border-black',
                            'down' => 'bg-red-500 hover:bg-red-400 border border-black',
                            'paused' => 'bg-yellow-500 hover:bg-yellow-400 border border-black',
                            default => 'bg-gray-700 hover:bg-gray-600 border border-black'
                        };
                    @endphp
                    <div class="{{ $colorClass }} h-full transition-colors cursor-help"
                        style="width: {{ 100 / count($logs) }}%;" title="{{ $log->status }} - {{ $log->created_at }}"></div>
                @endforeach
                @if($logs->isEmpty())
                    <div class="w-full h-full flex items-center justify-center text-xs text-gray-500">No data available yet
                    </div>
                @endif
            </div>
            <div class="flex justify-between mt-2 text-xs text-gray-500 font-mono">
                <span>Latest</span>
                <span>History (Last 10 checks)</span>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Configuration Form -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-2xl p-6">
                <h2 class="text-lg font-semibold text-white mb-6 flex items-center gap-2">
                    <x-icon name="o-cog-6-tooth" class="w-5 h-5 text-purple-400" />
                    Configuration
                </h2>

                <x-form wire:submit="save" no-separator class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-1 md:col-span-2">
                            <x-input label="Monitor Name" wire:model="name" placeholder="e.g. Production API"
                                class="bg-gray-900/50 border-gray-700 text-white placeholder-gray-500 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" />
                            <p class="text-xs text-gray-500 mt-1">A name to identify this monitor in your dashboard.</p>
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <x-input label="URL to Monitor" wire:model="url"
                                placeholder="https://api.example.com/health"
                                class="bg-gray-900/50 border-gray-700 text-white placeholder-gray-500 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 font-mono text-sm" />
                        </div>

                        <x-select label="Monitor Type" wire:model="type" :options="$types" option-label="name"
                            option-value="id"
                            class="bg-gray-900/50 border-gray-700 text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500" />

                        <x-select label="Check Interval" wire:model="interval" :options="$intervals"
                            placeholder="Select frequency"
                            class="bg-gray-900/50 border-gray-700 text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500" />

                        <!-- HTTP Options -->
                        <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 p-4 rounded-xl border border-gray-700/30 bg-gray-900/20"
                            x-show="$wire.type === 'http'" x-transition>

                            <div class="col-span-1 md:col-span-2 mb-2">
                                <span class="text-sm font-semibold text-gray-300">Advanced HTTP Settings</span>
                            </div>

                            <x-select label="HTTP Method" wire:model="method" :options="$methods"
                                class="bg-gray-900/50 border-gray-700 text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500" />

                            <x-select label="Timeout" wire:model="timeout" :options="$timeouts"
                                class="bg-gray-900/50 border-gray-700 text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500" />

                            <div class="col-span-1 md:col-span-2">
                                <x-select label="Success Condition" wire:model="condition" :options="$conditions"
                                    class="bg-gray-900/50 border-gray-700 text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500" />
                            </div>

                            <div class="col-span-1 md:col-span-2"
                                x-show="$wire.condition == '1' || $wire.condition == '2'" x-transition>
                                <x-input label="Keyword to search" wire:model="keyword"
                                    help-text="We will verify if this keyword is present in the response body."
                                    icon="o-magnifying-glass"
                                    class="bg-gray-900/50 border-gray-700 text-white placeholder-gray-500 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" />
                            </div>
                        </div>
                        <!-- Headers -->
                        <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 p-4 rounded-xl border border-gray-700/30 bg-gray-900/20"
                            x-show="$wire.type === 'http'" x-transition>

                            <div class="col-span-1 md:col-span-2 mb-2">
                                <span class="text-sm font-semibold text-gray-300">Headers</span>
                            </div>

                            @foreach ($headers as $index => $header)
                                <div class="col-span-2 grid grid-cols-2 gap-4">
                                    <x-input placeholder="Key" wire:model="headers.{{ $index }}.key"
                                        class="bg-gray-900/50 border-gray-700 text-white placeholder-gray-500 focus:border-purple-500 " />
                                    <x-input placeholder="Value" wire:model="headers.{{ $index }}.value"
                                        class="bg-gray-900/50 border-gray-700 text-white placeholder-gray-500 focus:border-purple-500" />
                                </div>
                                @if (count($headers) >= 1)
                                    <div class="col-span-2 flex justify-end">
                                        <x-button icon="o-trash" class="btn-ghost text-red-500"
                                            wire:click="removeHeader({{ $index }})" />
                                    </div>
                                @endif
                            @endforeach
                            <div class="col-span-1 md:col-span-2 flex justify-start mt-2">
                                <x-button label="Add Header" icon="o-plus" class="btn-ghost text-purple-400"
                                    wire:click="addHeader" />
                            </div>
                        </div>
                        <!-- Parameters -->
                        <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 p-4 rounded-xl border border-gray-700/30 bg-gray-900/20"
                            x-show="$wire.type === 'http'" x-transition>
                            <div class="col-span-1 md:col-span-2 mb-2">
                                <span class="text-sm font-semibold text-gray-300">Parameters</span>
                            </div>
                            @foreach ($parameters as $index => $parameter)
                                <div class="col-span-2 grid grid-cols-2 gap-4">
                                    <x-input placeholder="Key" wire:model="parameters.{{ $index }}.key"
                                        class="bg-gray-900/50 border-gray-700 text-white placeholder-gray-500 focus:border-purple-500 " />
                                    <x-input placeholder="Value" wire:model="parameters.{{ $index }}.value"
                                        class="bg-gray-900/50 border-gray-700 text-white placeholder-gray-500 focus:border-purple-500" />
                                </div>
                                @if (count($parameters) >= 1)
                                    <div class="col-span-2 flex justify-end">
                                        <x-button icon="o-trash" class="btn-ghost text-red-500"
                                            wire:click="removeParameter({{ $index }})" />
                                    </div>
                                @endif
                            @endforeach
                            <div class="col-span-1 md:col-span-2 flex justify-start mt-2">
                                <x-button label="Add Parameter" icon="o-plus" class="btn-ghost text-purple-400"
                                    wire:click="addParameter" />
                            </div>
                        </div>
                        <!-- Body -->
                        <div class="col-span-1 md:col-span-2 md:grid-cols-2 gap-6 p-4 rounded-xl border border-gray-700/30 bg-gray-900/20"
                            x-show="$wire.type === 'http'" x-transition>
                            <div class="col-span-1 md:col-span-2 mb-2">
                                <span class="text-sm font-semibold text-gray-300">Body</span>
                            </div>
                            @foreach ($body as $index => $value)
                                <x-textarea wire:model="body.{{ $index }}.body" rows="10"
                                    class="bg-gray-900/50 border-gray-700 text-white placeholder-gray-500 focus:border-purple-500 focus:ring-1 focus:ring-purple-500"
                                    placeholder='
                                        {
                                            "userId": 1,
                                            "id": 1,
                                            "title": "delectus aut autem",
                                            "completed": false
                                        }' />
                            @endforeach
                            @if(!$body)
                                <div class="col-span-1 md:col-span-2 flex justify-start mt-2">
                                    <x-button label="Add Body" icon="o-plus" class="btn-ghost text-purple-400"
                                        wire:click="addBody" wire:loading.attr="disabled" />
                                </div>
                            @endif
                        </div>
                    </div>

                    <x-slot:actions>
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-700/50 w-full">
                            @if ($id)
                                <x-button label="{{ $monitor->isPaused() ? 'Resume' : 'Pause' }}"
                                    class="btn-ghost text-gray-400 hover:text-white" wire:click="pause" />
                            @endif
                            <x-button label="Cancel" class="btn-ghost text-gray-400 hover:text-white"
                                wire:click="redirectBack" />
                            <x-button label="{{ $id ? 'Save Changes' : 'Create Monitor' }}"
                                class="bg-purple-600 hover:bg-purple-700 text-white border-none shadow-lg shadow-purple-900/20"
                                type="submit" spinner="save" />
                        </div>
                    </x-slot:actions>
                </x-form>
            </div>
        </div>

        <!-- Sidebar / Logs -->
        <div class="space-y-6">
            @if($id)
                <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-2xl p-6 h-fit">
                    <h3 class="text-lg font-semibold text-white mb-4">Recent Logs</h3>
                    <div class="rounded-xl overflow-hidden border border-gray-700/50">
                        <x-table :headers="$logHeaders" :rows="$logs" with-pagination wire:poll.30s
                            class="bg-gray-900/20 text-gray-300 text-sm">

                            @scope('cell_status', $log)
                            @php
                                $statusColor = match ($log->status) {
                                    'up' => 'text-green-400',
                                    'down' => 'text-red-400',
                                    default => 'text-yellow-400'
                                };
                                $icon = match ($log->status) {
                                    'up' => 'o-arrow-up',
                                    'down' => 'o-arrow-down',
                                    default => 'o-minus'
                                };
                            @endphp
                            <div class="flex items-center font-medium {{ $statusColor }}">
                                <x-icon name="{{ $icon }}" class="w-3 h-3 mr-1" />
                                {{ ucfirst($log->status) }}
                            </div>
                            @endscope

                            @scope('cell_response_time', $log)
                            <span class="font-mono text-gray-400">{{ $log->response_time }} ms</span>
                            @endscope

                            @scope('cell_created_at', $log)
                            <span class="text-xs text-gray-500">{{ $log->created_at->format('H:i') }}</span>
                            @endscope
                        </x-table>
                    </div>
                </div>
            @else
                <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-2xl p-6">
                    <h3 class="font-semibold text-white mb-2">Setup Guide</h3>
                    <ul class="space-y-4 text-sm text-gray-400">
                        <li class="flex gap-3">
                            <div class="bg-purple-500/10 p-1.5 rounded-lg h-fit shrink-0">
                                <span class="font-bold text-purple-400">1</span>
                            </div>
                            <span>Enter the <strong>URL</strong> you want to monitor (e.g., your website or API
                                endpoint).</span>
                        </li>
                        <li class="flex gap-3">
                            <div class="bg-purple-500/10 p-1.5 rounded-lg h-fit shrink-0">
                                <span class="font-bold text-purple-400">2</span>
                            </div>
                            <span>Select the <strong>Interval</strong>. Shorter intervals catch downtime faster but use more
                                resources.</span>
                        </li>
                        <li class="flex gap-3">
                            <div class="bg-purple-500/10 p-1.5 rounded-lg h-fit shrink-0">
                                <span class="font-bold text-purple-400">3</span>
                            </div>
                            <span>Configure <strong>Alerts</strong> in the settings menu to get notified when this service
                                goes down.</span>
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
    @if($id)
        <div class="flex flex-col mt-6">
            <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-2xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Performance Trend</h2>
                        <p class="text-sm text-gray-400">Response time history (Last 20 checks)</p>
                    </div>
                </div>

                <div class="h-80 relative w-full" x-data="{
        chart: null,
        init() {
        const ctx = this.$refs.canvas.getContext('2d');
        // Gradients
        const gradientUp = ctx.createLinearGradient(0, 0, 0, 300);
        gradientUp.addColorStop(0, 'rgba(168, 85, 247, 0.2)'); // Purple
        gradientUp.addColorStop(1, 'rgba(168, 85, 247, 0)');
        this.chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @js($chartLabels),
                datasets: [
                    {
                        label: 'Response Time (ms)',
                        backgroundColor: gradientUp,
                        borderColor: '#a855f7',
                        borderWidth: 2,
                        data: @js($chartData),
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: true,
                        usePointStyle: true,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' ms';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { color: '#6b7280', font: {family: 'Instrument Sans'} }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6b7280', font: {family: 'Instrument Sans'}, maxRotation: 0, autoSkip: true, maxTicksLimit: 10 }
                    }
                }
            }
        });
               Livewire.on('chart-updated', (data) => {
                   if (this.chart) {
                       this.chart.data.labels = data[0].labels;
                       this.chart.data.datasets[0].data = data[0].data;
                       this.chart.update();
                   }
               });
           }
        }" wire:ignore>
                    <canvas x-ref="canvas"></canvas>
                </div>
                <!-- Custom Legend -->
                <div class="flex items-center justify-center gap-6 mt-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-purple-500"></div>
                        <span class="text-sm text-gray-400">Response Time (ms)</span>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>