<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WateringEntry extends Model
{
    use HasFactory;
    protected $table = 'watering_entries';

    public function sensor_readings() {
        return $this->hasMany(SensorReading::class);
    }

    public function sensor_readings_chart_data() {
        $sensor_readings = DB::table('sensor_readings')->where('watering_entry_id', '=', $this->id)->select(['id', 'value', 'created_at'])->get();
        $labels = $sensor_readings->pluck('created_at')->toArray();
        $values = $sensor_readings->pluck('value')->toArray();
        return ['sensor_readings' => $values, 'labels' => $labels];
    }
}
