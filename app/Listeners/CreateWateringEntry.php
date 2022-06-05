<?php

namespace App\Listeners;

use App\Events\ControlDeviceSwitchedIsOnStatus;
use App\Models\WateringEntry;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateWateringEntry
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ControlDeviceSwitchedIsOnStatus  $event
     * @return void
     */
    public function handle(ControlDeviceSwitchedIsOnStatus $event)
    {
        $watering_entry = new WateringEntry([
        ]);
        $watering_entry->control_device_id = $event->control_device->id;
        $watering_entry->save();
    }
}
