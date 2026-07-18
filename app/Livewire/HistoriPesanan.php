<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class HistoriPesanan extends Component
{
    use WithFileUploads;

    public $payment_proof;
    public $selectedCheckoutId;

    public function uploadBukti($checkoutId)
    {
        $this->selectedCheckoutId = $checkoutId;
    }

    public function submitBukti()
    {
        $this->validate([
            'payment_proof' => 'required|image|max:3072',
        ]);

        // Find all orders with this checkout_id
        $orders = Order::where('checkout_id', $this->selectedCheckoutId)
            ->where('user_id', Auth::id())
            ->get();

        // Fallback for orders without checkout_id (using SINGLE- prefix)
        if ($orders->isEmpty() && str_starts_with($this->selectedCheckoutId, 'SINGLE-')) {
            $orderId = str_replace('SINGLE-', '', $this->selectedCheckoutId);
            $orders = Order::where('id', $orderId)
                ->where('user_id', Auth::id())
                ->get();
        }

        if ($orders->isEmpty()) {
            return;
        }

        $path = $this->payment_proof->store('payment-proofs', 'public');

        foreach ($orders as $order) {
            $order->update([
                'payment_proof' => $path,
                'status' => 'Menunggu Verifikasi',
            ]);
        }

        $this->payment_proof = null;
        $this->selectedCheckoutId = null;
        session()->flash('success', 'Bukti transfer berhasil dikirim! Mohon tunggu verifikasi admin.');
    }

    public function render()
    {
        $rawOrders = Order::where('user_id', Auth::id())
            ->with('obat')
            ->latest()
            ->get();

        // Group orders by checkout_id, falling back to ID if empty
        $groupedOrders = $rawOrders->groupBy(function ($order) {
            return $order->checkout_id ?: 'SINGLE-' . $order->id;
        });

        return view('livewire.histori-pesanan', [
            'groupedOrders' => $groupedOrders,
        ])->layout('layouts.app');
    }
}
