<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    protected $fillable = [
        'name',
        'category',
        'classification',
        'description',
        'dosage',
        'aturan_pakai',
        'side_effects',
        'warnings',
        'image',
        'price',
        'stock',
    ];

    public function gejalas()
    {
        return $this->belongsToMany(Gejala::class, 'obat_gejala');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
