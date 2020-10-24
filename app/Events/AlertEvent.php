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

use App\Alert;
use App\User;

use App\Repositories\AlertRepository;

class AlertEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    private $alert;

    public function __construct(Alert $alert)
    {
        $this->alert = $alert->refresh();
    }//end constructor method

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("alert.{$this->alert->receiver_id}");
    }//end methohd broadcastOn

    public function broadcastWith(){
        $alertRepository = new AlertRepository();

        return [
            "data" => $alertRepository->getHandledObjectData($this->alert)
        ];
    }//end method broadcastWith

    public function broadcastAs(){
        return "user.alert";
    }//end methohd broadcastAs

    public function broadcastWhen(){
        return $this->alert->receiver_type == User::class;
    }//end method broadcastWhen
}//end method AlertEvent
