<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ControlDevice extends Model
{
    use HasFactory;

    protected $table = 'control_devices';

    protected $fillable = [
        'name',
        'is_on',
    ];

    public function schedule() {
        return $this->hasOne(Schedule::class);
    }

    public function sensor_devices() {
        return $this->hasMany(SensorDevice::class);
    }
}
