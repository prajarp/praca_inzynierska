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
        'voivodeship',
        'expected_delivery_date',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }


    public function selectedOrders()
    {
        return $this->hasMany(SelectedOrder::class);
    }

    public function coordinates()
    {
        return $this->hasOne(Coordinates::class);
    }
}
