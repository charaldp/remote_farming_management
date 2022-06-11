<?php

namespace App\Listeners;

use App\Events\WateringEntryAdded;
use App\Models\WateringEntry;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SetupControlDeviceTurningOff implements ShouldQueue
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
     * @param  \App\Events\WateringEntryAdded  $event
     * @return void
     */
    public function handle(WateringEntryAdded $event)
    {
        $control_device = WateringEntry::find($event->watering_entry_id)->control_device;
        if ($event->watering_entry_id == $control_device->watering_entries->last()->id) {
            $control_device->is_on = false;
            $control_device->update();
        }
    }

    public $delay = 7200;
}
