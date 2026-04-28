<?php
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Plan;
use Livewire\Attributes\On;

new class extends Component {
    public $currentPlan = null;
    public $plans = [];

    public function mount()
    {
        $this->plans = Plan::where('is_active', true)->orderBy('id', 'asc')->get();
        $this->currentPlan = Auth::user()->plan_id;
    }

    public function selectPlan($planId)
    {
        $plan = collect($this->plans)->firstWhere('id', $planId);

        if ($plan->price == 0) {
            $user = Auth::user();
            $user->plan_id = $plan->id;
            $user->save();
            $this->currentPlan = $plan->id;
            session()->flash('success', 'Successfully subscribed to the Free plan!');
            $this->redirectRoute('dashboard', navigate: true);
        } else {
            $this->dispatch('initiate-paypal-checkout', [
                'paypalPlanId' => $plan->paypal_plan_id,
                'planId' => $plan->id,
            ]);
        }
    }

    #[On('payment-success')]
    public function handlePaymentSuccess($orderId, $planId)
    {
        // Add actual verification logic here if necessary
        $user = Auth::user();
        $user->plan_id = $planId;
        $user->save();
        $this->currentPlan = $planId;
        session()->flash('success', 'Payment successful! Your plan has been upgraded.');
        $this->redirectRoute('dashboard', navigate: true);
    }
};
?>

<div>
    <x-header title="Billing & Plans" subtitle="Choose the plan that fits your needs" size="lg" />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
        @foreach($plans as $plan)
            <x-card
                class="relative {{ $plan->is_popular ? 'border-purple-500 border-2' : 'border-gray-700' }} bg-gray-800/40 backdrop-blur-md">
                @if($plan->is_popular)
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2">
                        <span class="badge badge-primary bg-purple-600 border-none text-white text-xs">MOST POPULAR</span>
                    </div>
                @endif

                <x-slot:title>
                    <div class="text-center">
                        <h3 class="text-xl font-bold text-white">{{ $plan->name }}</h3>
                        <div class="mt-4">
                            @if($plan->price == 0)
                                <span class="text-4xl font-bold text-white">$0</span>
                                <span class="text-gray-400">/{{ $plan->interval }}</span>
                            @else
                                <span class="text-4xl font-bold text-white">${{ $plan->price }}</span>
                                <span class="text-gray-400">/{{ $plan->interval }}</span>
                            @endif
                        </div>
                    </div>
                </x-slot:title>

                <ul class="space-y-4">
                    @foreach($plan->features ?? [] as $feature)
                        <li class="flex items-center gap-2 text-gray-300">
                            <x-icon name="o-check-circle" class="w-5 h-5 text-green-400 flex-shrink-0" />
                            <span class="text-sm">{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>

                <x-slot:actions>
                    @if($currentPlan === $plan->id)
                        <x-button label="Current Plan" class="btn-success w-full" disabled />
                    @else

                        <x-button label="{{ $plan->price == 0 ? 'Select Plan' : 'Subscribe with PayPal' }}"
                            wire:click="selectPlan('{{ $plan->id }}')"
                            class="w-full {{ $plan->is_popular ? 'bg-purple-600 hover:bg-purple-700' : 'bg-gray-700 hover:bg-gray-600' }} text-white border-none" />
                    @endif
                </x-slot:actions>
            </x-card>
        @endforeach
    </div>

    <!-- PayPal Modal -->
    <dialog id="paypal_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-gray-800 border border-gray-700">
            <h3 class="font-bold text-lg text-white mb-4">Complete Subscription</h3>
            <p class="text-gray-400 mb-6 text-sm">Please securely complete your payment with PayPal to start your new plan.</p>
            <div id="paypal-modal-wrapper" class="w-full flex justify-center"></div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    @section('scripts')
        <script
            src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&vault=true&intent=subscription"
            data-sdk-integration-source="button-factory"></script>
    @endsection

    @script
    <script>
        $wire.on('initiate-paypal-checkout', (data) => {
            const plan = data[0];
            const wrapper = document.getElementById('paypal-modal-wrapper');
            const dynamicId = `paypal-button-container-${plan.paypalPlanId}`;

            // Clear previous buttons and inject the uniquely named container
            wrapper.innerHTML = `<div id="${dynamicId}" class="w-full flex justify-center"></div>`;
            
            // Show modal
            document.getElementById('paypal_modal').showModal();

            paypal.Buttons({
                style: {
                    shape: 'rect',
                    color: 'gold',
                    layout: 'horizontal',
                    label: 'subscribe'
                },
                createSubscription: function (data, actions) {
                    return actions.subscription.create({
                        /* Creates the subscription */
                        plan_id: plan.paypalPlanId
                    });
                },
                onApprove: function (data, actions) {
                    document.getElementById('paypal_modal').close();
                    // Call Livewire component method directly
                    $wire.handlePaymentSuccess(data.subscriptionID, plan.planId);
                },
                onCancel: function (data) {
                    document.getElementById('paypal_modal').close();
                }
            }).render(`#${dynamicId}`);
        });
    </script>
    @endscript
</div>