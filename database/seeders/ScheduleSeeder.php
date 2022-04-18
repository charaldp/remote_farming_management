<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::first();
        DB::table('schedules')->insert([
            'user_id' => $user->id,
            'name' => 'Typical Schedule',
            'watering_weekdays' => json_encode(['MO' => true, 'TH' => true]),
            'watering_weekdays_frequency' => json_encode(['MO' => 1, 'TH' => 1]),
            'watering_weekdays_time' => json_encode(['MO' => 7200, 'TH' => 7200]), //2 A.M. Midnight
            'watering_weekdays_duration' => json_encode(['MO' => 5400, 'TH' => 5400]),
        ]);
    }
}
