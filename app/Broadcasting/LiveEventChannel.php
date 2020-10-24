<?php

namespace App\Broadcasting;

use Auth;

use App\Repositories\LiveEventRepository;
use App\User;


class LiveEventChannel
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
    public function join(User $user, $liveEventId)
    {
        if(!Auth::check())
            return false;
        $liveEventRepository = new LiveEventRepository();
        $liveEvent = $liveEventRepository->getLiveEventById($liveEventId);

        if ($liveEvent == null) {
            return false;
        }

        if ($liveEventRepository->user($user)->liveEvent($liveEvent)->canJoinLiveEvent()) {
            return [
                "id" => $user->id,
                "name" => $user->name,
                "thumbnail" => $user->thumbnail,
                "is_creator" => $user->id == $liveEvent->user_id
            ];
        } else {
            return false;
        }
    } //end method join
}
