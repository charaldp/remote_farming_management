<?php

namespace App\Models;

use DateTime;
class Schedule extends BaseModel
{
    protected $table = 'schedules';

    protected $fillable = [
        'name',
        'watering_weekdays',
        'watering_weekdays_frequency',
        'watering_weekdays_time_hours',
        'watering_weekdays_time_minutes',
        'watering_weekdays_duration_hours',
        'watering_weekdays_duration_minutes',
    ];

    protected $casts = [
        'watering_weekdays' => 'array',
        'watering_weekdays_frequency' => 'array',
        'watering_weekdays_time_hours' => 'array',
        'watering_weekdays_time_minutes' => 'array',
        'watering_weekdays_duration_hours' => 'array',
        'watering_weekdays_duration_minutes' => 'array',
    ];

    public static $schedule_example = [
        'watering_weekdays' => ['SUN' => true],
        'watering_weekdays_frequency' => ['SUN' => 1],
        'watering_weekdays_time_hours' => ['SUN' => 2],
        'watering_weekdays_time_minutes' => ['SUN' => 40],
        'watering_weekdays_duration_hours' => ['SUN' => 1],
        'watering_weekdays_duration_minutes' => ['SUN' => 30],
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

    public function subscribed_devices() {
        return $this->hasMany(ControlDevice::class, 'id');
    }

    public function get_watering_weekdays() {
        $watering_weekdays = [];
        foreach ($this->watering_weekdays as $day => $is_enabled) {
            if ($is_enabled)
                $watering_weekdays[$day] = $is_enabled;
        }
        return $watering_weekdays;
    }

    public function getStartCrons() {
        $crons = [];
        foreach($this->watering_weekdays as $weekday => $enabled) {
            if ($enabled
                && array_key_exists($weekday, $this->watering_weekdays_time_hours)
                && array_key_exists($weekday, $this->watering_weekdays_time_minutes)
                && array_key_exists($weekday, $this->watering_weekdays_duration_hours)
                && array_key_exists($weekday, $this->watering_weekdays_duration_minutes)
            ) {
                $crons[$weekday] = (object)[
                    'hour' => $this->watering_weekdays_time_hours[$weekday],
                    'minute' => $this->watering_weekdays_time_minutes[$weekday],
                    'weekday' => $weekday,
                ];
            }
        }
        return $crons;
    }

    public function getStopCrons() {
        $crons = [];
        foreach($this->watering_weekdays as $weekday => $enabled) {
            if ($enabled
                && array_key_exists($weekday, $this->watering_weekdays_time_hours)
                && array_key_exists($weekday, $this->watering_weekdays_time_minutes)
                && array_key_exists($weekday, $this->watering_weekdays_duration_hours)
                && array_key_exists($weekday, $this->watering_weekdays_duration_minutes)
            ) {
                $datetime = new DateTime();
                $datetime->setTime(
                    $this->watering_weekdays_time_hours,
                    $this->watering_weekdays_time_minutes,
                    0,
                    0,
                    0
                );
                dd($datetime);
                $crons[$weekday] = (object)[
                    'hour' => $this->watering_weekdays_time_hours[$weekday],
                    'minute' => $this->watering_weekdays_time_minutes[$weekday],
                    'weekday' => $weekday,
                ];
            }
        }
        return $crons;
    }

}
