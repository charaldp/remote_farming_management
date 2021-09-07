<?php

use Illuminate\Database\Seeder;

class SensorDeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sensor_devices')->insert([
            'name' => 'Pressure Reader',
        ]);
    }
}
