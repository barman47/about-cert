<?php
namespace App\Repositories;

use App\Exceptions\LikeRepositoryException;

use App\Like;
use App\User;
use App\Post;
use App\Comment;
use App\Opportunity;

use App\Events\PostLikedOrUnlikedEvent;

class LikeRepository{

    const TYPE_MAPPING = [
        "post" => Post::class,
        "comment" => Comment::class,
        "opportunity" => Opportunity::class
    ];
    
    private $user;
    private $type;
    private $id;

    public function user(User $user): LikeRepository{
        $this->user = $user;
        return $this;
    }//end method user

    public function type(string $type): LikeRepository{
        if(!array_key_exists(strtolower($type), static::TYPE_MAPPING))
            throw new LikeRepositoryException("Invalid Array Type");
        
        $this->type = $type;
        return $this;
    }//end method type

    public function id(string $id): LikeRepository{
        $this->id = $id;
        return $this;
    }//end metho id

    public function commit() : bool{
        $class = static::TYPE_MAPPING[$this->type];
        $model = $class::find($this->id);

        if($model == null)
            throw new LikeRepositoryException("$type does not exist", 404);

        $like = $this->user->likes()->where([
                ["likeable_id", $this->id],
                ["likeable_type", $class]
            ])->first();

        $returnVal = false;

        if($like !== null){
            $like->delete();
            $returnVal = false;
        }else{
            $this->user->likes()->create([
                "likeable_id" => $this->id,
                "likeable_type" => $class,
            ]);

            $returnVal = true;
        }
        $this->broadcastLikeOrUnlike($model);

        return $returnVal;
    }//end method create

    private function broadcastLikeOrUnlike($model){
        if(get_class($model) == Post::class){
            broadcast(new PostLikedOrUnlikedEvent($model))->toOthers();
        }
    }//end method broadcastLikeOrUnlike
}//end class LikeRepository