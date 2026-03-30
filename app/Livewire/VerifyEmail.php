<?php

namespace App\Livewire\Auth;

use Livewire\Component;

class VerifyEmail extends Component
{
    public string $status = '';

    public function resend(): void
    {
        if (auth()->user()->hasVerifiedEmail()) {
            $this->redirect(route('home'));

            return;
        }

        auth()->user()->sendEmailVerificationNotification();

        $this->status = 'Лист надіслано повторно!';
    }

    public function render()
    {
        return view('livewire.verify-email');
    }
}
