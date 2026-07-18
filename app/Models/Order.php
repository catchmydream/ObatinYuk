<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'obat_id',
        'quantity',
        'total_price',
        'service_fee',
        'checkout_id',
        'shipping_address',
        'phone_number',
        'payment_method',
        'payment_proof',
        'payment_deadline',
        'status',
    ];

    protected $casts = [
        'payment_deadline' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }
}
