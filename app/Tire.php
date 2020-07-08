<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tire extends Model
{
    //
    protected $casts = [
        'type_dimensions' => 'array',
    ];
}
