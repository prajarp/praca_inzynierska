<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trailer extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'height',
        'width',
        'length',
        'max_weight'
    ];

    // calculate trailer volume
    public function calculateVolume()
    {
        return $this->height * $this->width * $this->length;
    }
}
