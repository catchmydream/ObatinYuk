<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Obat;

class GlobalSearch extends Component
{
    public $query = '';
    public $results = [];

    public function updatedQuery()
    {
        if (strlen($this->query) > 1) {
            $this->results = Obat::where('name', 'like', '%' . $this->query . '%')
                                 ->orWhere('description', 'like', '%' . $this->query . '%')
                                 ->take(5)
                                 ->get();
        } else {
            $this->results = [];
        }
    }

    public function render()
    {
        return view('livewire.global-search');
    }
}
