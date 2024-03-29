<?php

namespace Database\Seeders;

use App\Models\ControlDevice;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SensorDeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::first();
        $control_device = ControlDevice::first();
        DB::table('sensor_devices')->insert([
            'user_id' => $user->id,
            'control_device_id' => $control_device->id,
            'type' => 'pressure',
            'name' => 'Pressure Reader',
        ]);
    }
}
