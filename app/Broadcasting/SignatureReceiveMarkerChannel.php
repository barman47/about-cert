<?php

namespace App\Broadcasting;

use App\User;
use App\SignatureSendMarker;
use App\SignatureReceiveMarker;

class SignatureReceiveMarkerChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\User  $user
     * @return array|bool
     */
    public function join(User $user, SignatureReceiveMarker $signatureReceiveMarker)
    {
        return $user->id == $signatureReceiveMarker->receiver_id;
    }//end method join
}
