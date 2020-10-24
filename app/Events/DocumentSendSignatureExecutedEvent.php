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

use App\SignatureSendMarker;
use App\User;

class DocumentSendSignatureExecutedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    private $signatureSendMarker;

    public function __construct(SignatureSendMarker $signatureSendMarker)
    {
        $this->signatureSendMarker = $signatureSendMarker;
    }//end constructor method

    public function broadcastOn()
    {
        return new PrivateChannel("signature.send.marker.{$this->signatureSendMarker->id}");
    }

    public function broadcastWith(){
        return [];
    }//end method broadcastWith

    public function broadcastAs(){
        return "document.executed";
    }//end methohd broadcastAs

    public function broadcastWhen(){
        return true;
    }//end method broadcastWhen
}
