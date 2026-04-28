<?php
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

new class extends Component {
    public $name;
    public $email;
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ]);

        auth()->user()->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        $this->dispatch('swal', [
            'type' => 'success',
            'title' => 'Profile updated successfully!'
        ]);
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($this->current_password, auth()->user()->password)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }

        auth()->user()->update([
            'password' => bcrypt($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        $this->dispatch('swal', [
            'type' => 'success',
            'title' => 'Password updated successfully!'
        ]);
    }
};
?>

<div>
    <x-header title="Settings" subtitle="Manage your account settings" size="lg" />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Profile Settings -->
        <x-card title="Profile Information" subtitle="Update your account details"
            class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50">
            <x-form wire:submit="updateProfile">
                <x-input label="Name" wire:model="name" icon="o-user" required
                    class="bg-gray-900/50 border-gray-700 text-white" />
                <x-input label="Email" wire:model="email" type="email" icon="o-envelope" required
                    class="bg-gray-900/50 border-gray-700 text-white" />

                <x-slot:actions>
                    <x-button type="submit" label="Save Changes" icon="o-check"
                        class="bg-purple-600 hover:bg-purple-700 text-white border-none" spinner="updateProfile" />
                </x-slot:actions>
            </x-form>
        </x-card>

        <!-- Password Settings -->
        <x-card title="Change Password" subtitle="Update your password"
            class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50">
            <x-form wire:submit="updatePassword">
                <x-input label="Current Password" wire:model="current_password" type="password" icon="o-key" required
                    class="bg-gray-900/50 border-gray-700 text-white" />
                <x-input label="New Password" wire:model="new_password" type="password" icon="o-key" required
                    class="bg-gray-900/50 border-gray-700 text-white" />
                <x-input label="Confirm New Password" wire:model="new_password_confirmation" type="password" icon="o-check-circle" required
                    class="bg-gray-900/50 border-gray-700 text-white" />

                <x-slot:actions>
                    <x-button type="submit" label="Update Password" icon="o-check"
                        class="bg-purple-600 hover:bg-purple-700 text-white border-none" spinner="updatePassword" />
                </x-slot:actions>
            </x-form>
        </x-card>
    </div>
</div>
