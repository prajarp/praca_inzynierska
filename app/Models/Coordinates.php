<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coordinates extends Model
{
    use HasFactory;

    protected $fillable = [
        'longitude',
        'latitude',
        'order_id'
    ];

    /**
     * Definicja relacji jeden do jednego z modelem Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
