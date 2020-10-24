<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use App\Broadcasting\AlertChannel;
use App\Broadcasting\AllPostChannel;
use App\Broadcasting\PostChannel;
use App\Broadcasting\LiveEventChannel;
use App\Broadcasting\AuthenticatedChannel;
use App\Broadcasting\SignatureSendMarkerChannel;
use App\Broadcasting\SignatureReceiveMarkerChannel;

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel("test-event",function(){
    return Auth::check();
});

Broadcast::channel("alert.{userId}", AlertChannel::class);
Broadcast::channel("posts", AllPostChannel::class);
Broadcast::channel("post.{postId}", PostChannel::class);
Broadcast::channel("live.event.{liveEventId}", LiveEventChannel::class);
Broadcast::channel("authenticated", AuthenticatedChannel::class);
Broadcast::channel("signature.send.marker.{signatureSendMarker}", SignatureSendMarkerChannel::class);
Broadcast::channel("signature.receive.marker.{signatureReceiveMarker}", SignatureReceiveMarkerChannel::class);
