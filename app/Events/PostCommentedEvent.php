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

use App\Repositories\CommentRepository;
use App\Comment;
use App\Post;

class PostCommentedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    private $post;
    private $comment;

    public function __construct(Post $post, Comment $comment)
    {
        $this->post = $post;
        $this->comment = $comment;
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
        return "post.commented";
    }//end method broadcastAs

    public function broadcastWith(){
        $commentRepository = new CommentRepository();
        return [
            "id" => $this->post->id,
            "comment" => $commentRepository->getCommentWithData($this->comment->id)
        ];
    }//end method broadcastWith
}
