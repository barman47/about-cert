<?php
namespace App\Repositories;

use App\User;
use App\Share;
use App\Post;

use App\Interfaces\ShareableInterface;
use App\Events\PostSharedEvent;

class SharingRepository {
    private $user;
    private $share;

    public function __construct(){
        $this->share = new Share;
    }//end constructor

    public function user(User $user): SharingRepository{
        $this->user = $user;
        $this->share->user_id = $user->id;
        return $this;
    }//end method user

    public function shareable(ShareableInterface $shareable): SharingRepository{
        $this->share->shareable_id = $shareable->id;
        $this->share->shareable_type = get_class($shareable);

        return $this;
    }//end method shareable

    public function create(): bool{

        $temp = Share::where([
            ["user_id", $this->share->user_id],
            ["shareable_id", $this->share->shareable_id],
            ["shareable_type", $this->share->shareable_type],
        ])->first();

        if($temp == null)
            $this->share->save();
        else return false;

        if($this->share->shareable_type == Post::class){
            broadcast(new PostSharedEvent(Post::find($this->share->shareable_id)))->toOthers();
        }

        return true;
    }//end method create
}//end class SharingRepository