@if (session('success'))
<x-alert icon="o-check" class="alert-success" dismissible>
    {{ session('success') }}
</x-alert>
@endif
@if (session('error'))
<x-alert title="Error" description="{{ session('error') }}" icon="o-exclamation-triangle" dismissible/>
@endif
@if (session('warning'))
<x-alert icon="o-exclamation-triangle" class="alert-warning" dismissible>
    {{ session('warning') }}
</x-alert>
@endif
@if (session('info'))
<x-alert icon="o-info" class="alert-info" dismissible>
    {{ session('info') }}
</x-alert>
@endif
