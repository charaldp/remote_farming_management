<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(ScheduleSeeder::class);
        $this->call(ControlDeviceSeeder::class);
        $this->call(SensorDeviceSeeder::class);
        $this->call(WateringEntrySeeder::class);
        // \App\Models\User::factory(10)->create();
    }
}
