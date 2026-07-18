<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['user_id', 'obat_id', 'quantity'];

    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
