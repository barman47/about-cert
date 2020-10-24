<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Post;

class PostSharedEvent implements ShouldBroadcast
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
        return "post.shared";
    }//end method broadcastAs

    public function broadcastWith(){
        return [
            "id" => $this->post->id,
            "shares_count" => $this->post->shares()->count()
        ];
    }//end method broadcastWith
}
