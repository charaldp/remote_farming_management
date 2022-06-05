<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WateringEntry extends Model
{
    use HasFactory;
    protected $table = 'watering_entries';

    public function sensor_readings() {
        $this->hasMany(SensorReading::class);
    }
}
