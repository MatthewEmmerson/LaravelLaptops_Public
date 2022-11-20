<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalOAuthAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'provider_name',
        'provider_id'
    ];

    // Link this external OAuth Account to one Laptop Site account
    public function user() {
        return $this->belongsTo('App\Models\User');
    }
}
