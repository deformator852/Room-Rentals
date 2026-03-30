<?php

namespace App\Livewire\Register;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Auth\Events\Registered;
use Livewire\Component;

class Form extends Component
{
    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    protected array $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
        'password_confirmation' => 'required',
    ];

    public function register(): void
    {
        $validated = $this->validate();
        $user = app(CreateNewUser::class)->create($validated);
        event(new Registered($user));
        auth()->login($user);
        $this->redirect(route('home'));
    }

    public function render()
    {
        return view('livewire.register.form');
    }
}
