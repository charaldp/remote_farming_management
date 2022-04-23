<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorDevice extends Model
{
    protected $table = 'sensor_devices';

    protected $fillable = [
        'type',
        'name',
    ];

    public function measurements() {
        return $this->hasMany(SensorReading::class);
    }

    public function control_device() {
        return $this->belongsTo(ControlDevice::class);
    }
}
