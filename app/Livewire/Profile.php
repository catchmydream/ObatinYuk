<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Profile extends Component
{
    public $name = '';
    public $email = '';
    public $phone_number = '';
    public $address = '';
    
    public $current_password = '';
    public $new_password = '';
    public $new_password_confirmation = '';

    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone_number = $user->phone_number;
            $this->address = $user->address;
        }
    }

    public function save()
    {
        $user = User::find(Auth::id());

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ];

        if ($this->new_password) {
            $rules['current_password'] = 'required';
            $rules['new_password'] = 'required|min:6|confirmed';
        }

        $this->validate($rules);

        if ($this->new_password) {
            if (!Hash::check($this->current_password, $user->password)) {
                $this->addError('current_password', 'Password saat ini salah.');
                return;
            }
            $user->password = Hash::make($this->new_password);
        }

        $user->name = $this->name;
        $user->email = $this->email;
        $user->phone_number = $this->phone_number;
        $user->address = $this->address;
        $user->save();

        // Clear password inputs
        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';

        $this->dispatch('swal:success', [
            'title' => 'Sukses!',
            'text' => 'Informasi sudah diperbarui'
        ]);
    }

    public function render()
    {
        return view('livewire.profile')->layout('layouts.app');
    }
}
