<?php

namespace App\Livewire\Login;

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

    public function login(): void
    {
        $this->validate();
        session()->flash('status', 'Акаунт створено!');
        $this->redirect(route('login'));
    }

    public function render()
    {
        return view('livewire.login.form');
    }
}
