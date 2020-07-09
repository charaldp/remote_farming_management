<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rim extends Model
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
                            "intrWidth" => 0.022,
                            "numRibs" => 1,
                            "tRib" => 0.015,
                            "dRib" => 0.030,
                            "ribsPosition" => 0.12,
                            "axleIntrWidth" =>-0.01,
                            "axleDI" => 0.01,
                            "axleDO" => 0.08,
                            "tAxle" => 0.02]
    ];
}
