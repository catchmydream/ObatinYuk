<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function authenticate()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            
            // Redirect admin to /admin, user to /chat
            if (Auth::user()->role === 'admin') {
                return redirect()->to('/admin');
            }
            
            return redirect()->intended('/');
        }

        $this->addError('email', 'Email atau password yang Anda masukkan salah.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.app');
    }
}
