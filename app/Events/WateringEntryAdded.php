<?php

namespace App\Events;

use App\Models\WateringEntry;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WateringEntryAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $watering_entry_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($watering_entry_id)
    {
        $this->watering_entry_id = $watering_entry_id;
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
