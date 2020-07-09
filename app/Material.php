<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materials';

    protected $casts = [
        'type_dimethree_material_options' => 'array',
    ];
    
    protected $attributes = [
        'type_dimethree_material_options' => ["shininess" => 50, "colour"  => "0x1b1b1b"],
    ];
}
