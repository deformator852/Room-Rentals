<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class Register extends Component
{
    public string $role = 'tenant';

    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    protected array $rules = [
        'role' => 'required|in:tenant,landlord',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
        'password_confirmation' => 'required',
    ];

    public function register(): void
    {
        $data = $this->validate();

        session()->flash('status', 'Акаунт створено!');
        $this->redirect(route('login.index'));
    }

    public function render()
    {
        return view('pages.auth.register');
    }
}
