<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\User;
use App\LiveEvent;

class LiveEventSendOfferEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    private $sender;
    private $receiver;
    private $liveEvent;
    private $offer;

    public function __construct(User $sender, User $receiver, LiveEvent $liveEvent, $offer)
    {
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->liveEvent = $liveEvent;
        $this->offer = $offer;
    }//end constructor method

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel("live.event.{$this->liveEvent->id}");
    }

    public function broadcastWith(){
        return [
            "to" => $this->receiver->id,
            "from" => $this->sender->id,
            "offer" => $this->offer,
            "on"    => $this->liveEvent->id,
            "is_creator" => $this->liveEvent->user_id == $this->sender->id
        ];
    }//end method broadcastWith

    public function broadcastAs(){
        return "receive.offer";
    }//end methohd broadcastAs

    public function broadcastWhen(){
        return true;
    }//end method broadcastWhen
}
