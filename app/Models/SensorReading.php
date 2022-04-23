<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    use HasFactory;

    protected $table = 'sensor_reading';

    protected $fillable = [
        'measurement_type',
        'value',
        'measured_at',
    ];

    public static $measurement_types = [
        'pressure',
    ];

    public function sensor_device() {
        $this->belongsTo(SensorDevice::class);

    }
}