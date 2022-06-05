<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    use HasFactory;

    protected $table = 'sensor_readings';

    protected $fillable = [
        'measurement_type',
        'value',
        'measured_at',
    ];

    public static $measurement_types = [
        'pressure',
        'temperature',
    ];

    public function sensor_device() {
        $this->belongsTo(SensorDevice::class);
    }

    public function watering_entry() {
        $this->belongsTo(WateringEntry::class);
    }
}
