<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tire extends Model
{
    //
    protected $table = 'rims';

    protected $casts = [
        'type_dimensions' => 'array',
    ];
    
    protected $attributes = [
        'type_dimensions' => ["DO" => 0.43,
                            "DI" => 0.4,
                            "t" => 0.15,
                            ]
    ];
}
