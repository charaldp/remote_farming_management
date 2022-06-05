<?php

namespace App\Events;

use App\Models\SensorReading;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SensorReadingAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels, InteractsWithBroadcasting;

    protected $sensor_readings_data;
    protected $control_device_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(SensorReading $sensor_reading)
    {
        // dd($sensor_reading->watering_entry->sensor_readings->select(['id', 'value', 'created_at']));
        $this->sensor_readings_data = $sensor_reading->watering_entry->sensor_readings_chart_data();
        $this->control_device_id = $sensor_reading->sensor_device->control_device->id;
        $this->broadcastVia('pusher');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('control_device.'.$this->control_device_id.'.sensor_reading');
    }

    public function broadcastWith()
    {
        return ['sensor_readings_data' => $this->sensor_readings_data];
    }
}
