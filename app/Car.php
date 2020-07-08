<?php

namespace App;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use SpatialTrait;

    protected $spatialFields = [
        'body_points',
        'wheel_center_positions'
    ];
}
