<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SelectedOrder extends Model
{
    protected $fillable = [
        'order_id'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
