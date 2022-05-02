<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class Schedule extends BaseModel
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
        'SUN' => "SUNDAY",
        'MON' => "MONDAY",
        'TUE' => "TUESDAY",
        'WED' => "WEDNESDAY",
        'THU' => "THURSDAY",
        'FRI' => "FRIDAY",
        'SAT' => "SATURDAY",
    ];

    public function getWeekdayTimeHour($key): int
    {
        return floor($this->watering_weekdays_time[$key] / 3600);
    }

    public function getWeekdayTimeMinute($key): int
    {
        return floor(($this->watering_weekdays_time[$key] % 3600) / 60);
    }


    public function weekdays()
    {
        return array_filter($this->watering_weekdays);
    }

    public function weekday($key)
    {
        return self::$weekMap[$key];
    }
}
