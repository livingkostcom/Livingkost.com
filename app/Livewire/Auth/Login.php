<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Login extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|min:8')]
    public string $password = '';

    public bool $remember = false;

    public function mount()
    {
        // Redirect if already authenticated
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
    }

    public function login()
    {
        $this->validate();

        if (Auth::attempt($this->only(['email', 'password']), $this->remember)) {
            return redirect()->intended(route('dashboard'));
        }

        $this->addError('email', 'Email atau password tidak sesuai.');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.guest');
    }
}
