<?php

namespace App\Livewire;

use App\Models\CartItem;
use App\Models\Obat;
use App\Models\Gejala;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Katalog extends Component
{
    use WithPagination;
    #[Url]
    public $q = '';

    #[Url]
    public $category = '';

    #[Url]
    public $in_stock = false;

    #[Url]
    public $symptom = '';

    #[Url]
    public $perPage = 8;

    public function mount()
    {
        // ...
    }

    public function clearSearch()
    {
        $this->q = '';
        $this->resetPage();
    }

    public function goToProduct($id)
    {
        return redirect('/produk/' . $id);
    }

    public function addToCart($obatId)
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

        $this->dispatch('cart-updated');
    }

    public function searchBySymptom($symptomId = '')
    {
        $this->symptom = $symptomId;
        $this->resetPage();
        $this->dispatch('scroll-to-products');
    }

    public function updatedQ() { $this->resetPage(); }
    public function updatedCategory() { $this->resetPage(); }
    public function updatedSymptom() { $this->resetPage(); }
    public function updatedInStock() { $this->resetPage(); }
    public function updatedPerPage() { $this->resetPage(); }

    public function render()
    {
        $query = Obat::with('gejalas');

        if (!empty($this->q)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->q . '%')
                  ->orWhere('description', 'like', '%' . $this->q . '%');
            });
        }

        if (!empty($this->category)) {
            $query->where('category', $this->category);
        }

        if ($this->in_stock) {
            $query->where('stock', '>', 0);
        }

        if (!empty($this->symptom)) {
            $query->whereHas('gejalas', function ($q) {
                $q->where('gejalas.id', $this->symptom);
            });
        }

        $limit = ($this->perPage === 'all' || $this->perPage == 100) ? 100 : (int)$this->perPage;
        $obats = $query->paginate($limit);
        $gejalas = Gejala::orderBy('name')->get();

        return view('livewire.katalog', [
            'obats'          => $obats,
            'gejalasList'    => $gejalas,
        ])->layout('layouts.app');
    }
}
