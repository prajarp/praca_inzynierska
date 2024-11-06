<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'item_type', // 'window' lub 'element'
        'weight',
        'height',
        'width',
        'length'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function calculateArea()
    {
        return $this->width * $this->length;
    }
}
