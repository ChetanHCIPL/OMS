<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SendOtpNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $section_type;
    public $type;

    /**
     * Create a new event instance.
     *
     * @return void X:\app\Events\SendOtpNotification
     */
    public function __construct($notification)
    {
        $this->section_type         = $notification['section_type'];
        $this->type                 = $notification['type'];
        $this->otp                  = $notification['otp'];
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
