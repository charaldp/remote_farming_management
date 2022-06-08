<?php

namespace App\Listeners;

use App\Events\ControlDeviceSwitchedIsOnStatus;
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
     * @param  \App\Events\ControlDeviceSwitchedIsOnStatus  $event
     * @return void
     */
    public function handle(ControlDeviceSwitchedIsOnStatus $event)
    {
        // $this->delay = $event->...;
        $event->control_device->is_on = false;
        $event->control_device->update();
    }

    public $delay = 7200;
}
