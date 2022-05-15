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
            'watering_weekdays_time' => json_encode(['MON' => 7200, 'THU' => 7200]), //2 A.M. Midnight
            'watering_weekdays_duration' => json_encode(['MON' => 5400, 'THU' => 5400]),
        ]);
    }
}
