<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public $avatar;

    public bool $emailChanged = false;

    protected function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.Auth::id(),
            'avatar' => 'nullable|image|max:2048',
        ];
    }

    public function mount(): void
    {
        $user = Auth::user();

        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
    }

    public function updatedEmail(): void
    {
        $this->emailChanged = $this->email !== Auth::user()->email;
    }

    public function save(): void
    {
        $this->validate();

        $user = Auth::user();

        if ($this->avatar) {
            if ($user->avatar_url) {
                Storage::disk('public')->delete($user->avatar_url);
            }
            $path = $this->avatar->store('avatars', 'public');
            $user->avatar_url = $path;
        }

        if ($this->emailChanged) {
            $user->email_verified_at = null;
            $user->sendEmailVerificationNotification();
        }

        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->email = $this->email;
        $user->save();

        session()->flash('status', 'Профіль оновлено!');
    }

    public function render()
    {
        return view('livewire.profile.edit');
    }
}
