<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\LiveEvent;
use App\User;
use App\Parasite;

class LiveEventCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    private $liveEvent;

    public function __construct(LiveEvent $liveEvent)
    {
        $this->liveEvent = $liveEvent;
    }//end constructor method

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $liveEvent = $this->liveEvent;
        $user = User::find($liveEvent->user_id);

        $canJoin = json_decode($liveEvent->can_join);

        $channels = [];
        $userIds = [];

        if(in_array("all", $canJoin)){
            return new PrivateChannel("authenticated");
        }

        foreach($canJoin as $value){
            if($value == "followers"){
                $userIds = array_merge($userIds, Parasite::where("host", $user->id)->pluck("parasite")->toArray());
                continue;
            }

            $userIds[] = $value;
        }

        $userIds = array_unique($userIds);
        

        foreach($userIds as $id){
            $channels[] = new PrivateChannel("user.".$id);
        }

        return $channels;
    }

    public function broadcastWith(){
        return [
            "id" => $this->liveEvent->id
        ];
    }//end method broadcastWith

    public function broadcastAs(){
        return "live.event.created";
    }//end methohd broadcastAs

    public function broadcastWhen(){
        return true;
    }//end method broadcastWhen
}
