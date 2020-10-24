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

use App\SignatureReceiveMarker;
use App\User;

class DocumentReceiveSignatureViewedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    private $email;
    private $signatureReceiveMarker;

    public function __construct(SignatureReceiveMarker $signatureReceiveMarker, string $email)
    {
        $this->signatureReceiveMarker = $signatureReceiveMarker;
        $this->email = $email;
    }//end constructor method

    public function broadcastOn()
    {
        return new PrivateChannel("signature.receive.marker.{$this->signatureReceiveMarker->id}");
    }

    public function broadcastWith(){
        return [
            "signer" => [
                "email" => $this->email
            ]
        ];
    }//end method broadcastWith

    public function broadcastAs(){
        return "document.viewed";
    }//end methohd broadcastAs

    public function broadcastWhen(){
        return true;
    }//end method broadcastWhen
}
