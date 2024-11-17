<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_name',
        'delivery_address',
        'expected_delivery_date',
        'window_quantity',
        'other_elements_quantity',
        'windows_weight',
        'total_weight',
        'window_area',
        'window_dimensions'
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getWindowsWeightAttribute()
    {
        return $this->orderItems->where('item_type', 'window')->sum('weight');
    }
}
