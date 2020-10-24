<?php

namespace App\Broadcasting;

use App\User;
use App\SignatureSendMarker;

class SignatureSendMarkerChannel
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
    public function join(User $user, SignatureSendMarker $signatureSendMarker)
    {
        return $user->id == $signatureSendMarker->user_id;
    }
}
