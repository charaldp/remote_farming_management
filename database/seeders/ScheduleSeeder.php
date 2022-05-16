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
            'watering_weekdays' => json_encode(['MON' => true, 'THU' => true]),
            'watering_weekdays_frequency' => json_encode(['MON' => 1, 'THU' => 1]),
            'watering_weekdays_time_hours' => json_encode(['MON' => 2, 'THU' => 2]),
            'watering_weekdays_time_minutes' => json_encode(['MON' => 15, 'THU' => 15]),
            'watering_weekdays_duration_hours' => json_encode(['MON' => 1, 'THU' => 1]),
            'watering_weekdays_duration_minutes' => json_encode(['MON' => 30, 'THU' => 30]),
        ]);
    }
}
