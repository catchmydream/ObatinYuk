<?php

namespace App\Livewire;

use App\Models\CartItem;
use App\Models\Obat;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProductDetail extends Component
{
    public Obat $obat;
    public $quantity = 1;

    public function mount($id)
    {
        $this->obat = Obat::with('gejalas')->findOrFail($id);
    }

    public function incrementQty()
    {
        if ($this->quantity < $this->obat->stock) {
            $this->quantity++;
        }
    }

    public function decrementQty()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $this->validate([
            'quantity' => 'required|integer|min:1|max:' . $this->obat->stock,
        ]);

        $existing = CartItem::where('user_id', Auth::id())
            ->where('obat_id', $this->obat->id)
            ->first();

        if ($existing) {
            $existing->update([
                'quantity' => $existing->quantity + $this->quantity
            ]);
        } else {
            CartItem::create([
                'user_id' => Auth::id(),
                'obat_id' => $this->obat->id,
                'quantity' => $this->quantity,
            ]);
        }

        session()->flash('cart_success', $this->obat->name . ' (' . $this->quantity . ' item) berhasil ditambahkan ke keranjang!');
        $this->quantity = 1; // Reset quantity after adding
    }

    public function render()
    {
        $relatedObats = Obat::where('id', '!=', $this->obat->id)
            ->whereHas('gejalas', function ($q) {
                $q->whereIn('gejalas.id', $this->obat->gejalas->pluck('id'));
            })
            ->take(4)
            ->get();

        return view('livewire.product-detail', [
            'relatedObats' => $relatedObats,
        ])->layout('layouts.app');
    }
}
