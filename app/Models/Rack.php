<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rack extends Model
{
    use HasFactory;

    protected $fillable = [
        'rack_type',
        'outer_height',
        'outer_width',
        'outer_length',
        'loading_height',
        'loading_width',
        'loading_length',
        'net_weight',
    ];
}
