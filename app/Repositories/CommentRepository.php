<?php
namespace App\Repositories;

use App\Comment;
use App\Exceptions\CommentRepositoryException;
use App\Post;
use App\User;

use App\Events\PostCommentedEvent;

class CommentRepository
{
    const TYPE_MAPPER = [
        'post' => Post::class,
        'comment' => Comment::class,
    ];

    private $user;
    private $model;
    private $content;

    public function user(User $user): CommentRepository
    {
        $this->user = $user;
        return $this;
    }//end method user

    function model(string $type, string $id): CommentRepository
    {
        $type = strtolower($type);
        if (!array_key_exists($type, static::TYPE_MAPPER)) {
            throw new CommentRepositoryException("Unsupported type '$type'. Supported values: " . implode(array_keys(static::TYPE_MAPPER), '|'), 432);
        }

        $this->model = static::TYPE_MAPPER[$type]::find($id);

        if ($this->model == null) {
            throw new CommentRepositoryException("$type not found", 404);
        }

        if ($type == 'comment') {
            if ($this->model->commentable_type == Comment::class) {
                throw new CommentRepositoryException('Error: Three layers of comment is forbidden', 400);
            }

        }

        return $this;
    } //end method type

    function content(string $content): CommentRepository
    {
        $this->content = $content;
        return $this;
    } //end method content

    function create(): Comment
    {
        if (!$this->user) {
            throw new CommentRepositoryException("User must be specified", 500);
        }

        $comment = $this->user->comments()->create([
            'content' => $this->content,
            'commentable_id' => $this->model->id,
            'commentable_type' => get_class($this->model),
        ]);

        $this->broadcastCommentedEvent($this->model, $comment);

        return $this->getCommentWithData($comment->id);
    } //end method create

    function broadcastCommentedEvent($model, Comment $comment)
    {
        if (get_class($model) == Post::class) {
            broadcast(new PostCommentedEvent($model, $comment))->toOthers();
        }
    } //end method broadcastCommentedEvent

    function getCommentWithData(string $id): Comment
    {
        return Comment::with('user:id,name,thumbnail')->where('id', $id)->first();
    } //end method getCommentWithData
} //end class CommentRepository
