<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trailer extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'height',
        'width',
        'length',
        'max_weight',
        'total_height',
        'total_length',
        'axle_weight',
        'total_weight',
    ];
}
