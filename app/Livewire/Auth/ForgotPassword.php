<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Lupa Password')]
class ForgotPassword extends Component
{
    public string $email = '';

    public function sendResetLink()
    {
        $this->validate([
            'email' => 'required|email'
        ]);

        // Logic reset password dapat diimplementasikan menggunakan default fortify/breeze
        // Karena ini mock-up/fase awal, kita kirim pesan sukses palsu menggunakan SweetAlert
        $this->dispatch('swal:success', [
            'title' => 'Terkirim!',
            'text' => 'Tautan reset password telah dikirim ke email Anda jika terdaftar di sistem kami.'
        ]);

        $this->reset('email');
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
