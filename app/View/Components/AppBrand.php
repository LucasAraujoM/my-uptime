<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AppBrand extends Component
{
    public function render(): View|Closure|string
    {
        return <<<'HTML'
                <a href="/" wire:navigate>
                    <!-- Full brand when expanded -->
                    <div {{ $attributes->class(["hidden-when-collapsed"]) }}>
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-8 h-8 rounded-lg object-contain">
                            <span class="font-bold text-xl tracking-tight text-white">MyUptime</span>
                        </div>
                    </div>

                    <!-- Only logo when collapsed -->
                    <div class="display-when-collapsed hidden mx-auto mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-8 h-8 rounded-lg object-contain mx-auto">
                    </div>
                </a>
            HTML;
    }
}
