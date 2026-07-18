<?php

namespace App\Livewire;

use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Keranjang extends Component
{
    public function addItem($obatId)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $existing = CartItem::where('user_id', Auth::id())
            ->where('obat_id', $obatId)
            ->first();

        if ($existing) {
            $existing->increment('quantity');
        } else {
            CartItem::create([
                'user_id' => Auth::id(),
                'obat_id' => $obatId,
                'quantity' => 1,
            ]);
        }

        session()->flash('cart_success', 'Obat berhasil ditambahkan ke keranjang!');
    }

    public function increment($cartItemId)
    {
        $item = CartItem::where('id', $cartItemId)->where('user_id', Auth::id())->firstOrFail();
        if ($item->quantity < $item->obat->stock) {
            $item->increment('quantity');
        }
    }

    public function decrement($cartItemId)
    {
        $item = CartItem::where('id', $cartItemId)->where('user_id', Auth::id())->firstOrFail();
        if ($item->quantity > 1) {
            $item->decrement('quantity');
        } else {
            $item->delete();
        }
    }

    public function remove($cartItemId)
    {
        CartItem::where('id', $cartItemId)->where('user_id', Auth::id())->delete();
    }

    public function checkout()
    {
        $items = CartItem::where('user_id', Auth::id())->with('obat')->get();

        if ($items->isEmpty()) {
            return;
        }

        return redirect('/checkout');
    }

    public function render()
    {
        $items = CartItem::where('user_id', Auth::id())->with('obat')->get();
        $total = $items->sum(fn($i) => $i->obat->price * $i->quantity);

        return view('livewire.keranjang', [
            'items' => $items,
            'total' => $total,
        ])->layout('layouts.app');
    }
}
