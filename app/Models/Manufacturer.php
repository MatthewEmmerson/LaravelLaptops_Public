<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    use HasFactory;

    public function laptops() {
        return $this->hasMany(Laptop::class);
    }

    protected $fillable = [
        'id',
        'name'
    ];
}
