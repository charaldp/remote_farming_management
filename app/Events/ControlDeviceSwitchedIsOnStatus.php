<?php

namespace App\Events;

use App\Models\ControlDevice;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ControlDeviceSwitchedIsOnStatus
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $control_device;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ControlDevice $control_device)
    {
        $this->control_device = $control_device;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
