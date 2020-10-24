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


use App\Post;

class PostLikedOrUnlikedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    private $post;

    public function __construct(Post $post)
    {
        $this->post = $post->refresh();
    }//end constructor method

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("post.{$this->post->id}");
    }//end method broadcastOn

    public function broadcastAs(){
        return "post.liked";
    }//end method broadcastAs

    public function broadcastWith(){
        return [
            "id" => $this->post->id,
            "likes_count" => $this->post->likes()->count()
        ];
    }//end method broadcastWith
}//end class PostLikedEvent
