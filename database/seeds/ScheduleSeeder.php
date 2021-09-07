<?php

use Illuminate\Database\Seeder;
use App\User;

class UserSeeder extends Seeder
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
            'watering_weekdays' => json_encode(['0' => '2','1' => '5']),
            'watering_weekdays_frequency' => json_encode(['0' => 1, '1' => 1]),
            'watering_weekdays_time' => json_encode(['0' => 7200, '1' => 7200]), //2 A.M. Midnight
            'watering_weekdays_duration' => json_encode(['0' => 5400, '1' => 5400]),
        ]);
    }
}
