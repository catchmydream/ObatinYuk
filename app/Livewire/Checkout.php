<?php

namespace App\Livewire;

use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Checkout extends Component
{
    use WithFileUploads;

    public $shipping_address;
    public $phone_number;
    public $payment_method = '';
    public $selected_bank = '';
    public $payment_proof;
    public $service_fee = 5000;

    public function mount()
    {
        $user = Auth::user();
        $this->shipping_address = $user->address;
        $this->phone_number = $user->phone_number;
    }

    public function processCheckout()
    {
        $this->validate([
            'shipping_address' => 'required|string',
            'phone_number' => 'required|string',
            'payment_method' => 'required|in:transfer_bank,cod',
        ]);

        if ($this->payment_method === 'transfer_bank') {
            $this->validate([
                'selected_bank' => 'required|in:BCA,BRI,CIMB Niaga',
            ]);
        }

        $items = CartItem::where('user_id', Auth::id())->with('obat')->get();

        if ($items->isEmpty()) {
            return redirect('/keranjang');
        }

        $user = Auth::user();
        if (empty($user->address) || empty($user->phone_number)) {
            $user->update([
                'address' => $this->shipping_address,
                'phone_number' => $this->phone_number,
            ]);
        }

        $paymentLabel = $this->payment_method === 'cod' 
            ? 'COD (Bayar di Tempat)' 
            : 'Transfer Bank - ' . $this->selected_bank;

        $checkoutId = 'INV-' . strtoupper(Str::random(8));
        
        $proofPath = null;
        
        foreach ($items as $index => $item) {
            // We add the service fee to the first item's record for database tracking
            $feeToApply = ($index === 0) ? $this->service_fee : 0;

            Order::create([
                'user_id' => Auth::id(),
                'obat_id' => $item->obat_id,
                'quantity' => $item->quantity,
                'total_price' => ($item->obat->price * $item->quantity) + $feeToApply,
                'service_fee' => $feeToApply,
                'checkout_id' => $checkoutId,
                'shipping_address' => $this->shipping_address,
                'phone_number' => $this->phone_number,
                'payment_method' => $paymentLabel,
                'payment_proof' => $proofPath,
                'status' => $this->payment_method === 'cod' ? 'Menunggu Verifikasi' : 'Menunggu Pembayaran',
                'payment_deadline' => now()->addHours(24),
            ]);
            
            $item->obat->decrement('stock', $item->quantity);
        }

        CartItem::where('user_id', Auth::id())->delete();

        session()->flash('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
        return redirect('/pesanan-saya');
    }

    public function render()
    {
        $items = CartItem::where('user_id', Auth::id())->with('obat')->get();
        $subtotal = $items->sum(fn($i) => $i->obat->price * $i->quantity);
        $total = $subtotal + $this->service_fee;

        if ($items->isEmpty()) {
            return redirect('/keranjang');
        }

        return view('livewire.checkout', [
            'items' => $items,
            'subtotal' => $subtotal,
            'total' => $total,
        ])->layout('layouts.app');
    }
}
