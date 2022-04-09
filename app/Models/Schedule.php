<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedules';

    protected $fillable = [
        'name',
        'watering_weekdays',
        'watering_weekdays_frequency',
        'watering_weekdays_time',
        'watering_weekdays_duration',
    ];

    protected $casts = [
        'watering_weekdays' => 'array',
        'watering_weekdays_frequency' => 'array',
        'watering_weekdays_time' => 'array',
        'watering_weekdays_duration' => 'array',
    ];

    public static $weekMap = [
        'SU' => "SUNDAY",
        'MO' => "MONDAY",
        'TU' => "TUESDAY",
        'WE' => "WEDNESDAY",
        'TH' => "THURSDAY",
        'FR' => "FRIDAY",
        'SA' => "SATURDAY",
    ];

    public function weekday($key) {
        return self::$weekMap[$this->watering_weekdays[$key]];
    }

}
