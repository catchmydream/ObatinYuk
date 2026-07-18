<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gejala extends Model
{
    protected $fillable = [
        'name',
    ];

    public function obats()
    {
        return $this->belongsToMany(Obat::class, 'obat_gejala');
    }
}
