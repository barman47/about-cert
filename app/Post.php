<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;
use App\Traits\MultipleDocumentable;
use App\Traits\ScopeExclude;
use App\Traits\Commentable;
use App\Traits\Likeable;
use App\Traits\CustomPaginate;

use App\Interfaces\TrackableInterface;

use App\Interfaces\ShareableInterface;
use Laravel\Scout\Searchable;

class Post extends Model implements TrackableInterface, ShareableInterface
{
    use UsesUuid, MultipleDocumentable, Commentable, Likeable;
    // use CustomPaginate, Searchable;
    use Searchable, ScopeExclude;

    protected $columns = array("id", "user_id", "content", "title", "allow_comments", "time", "category", "img", "video", "created_at", "updated_at");
    protected $guarded = [];
    protected $hidden = ["user_id"];
    public static $apiIndexRoute = "/posts";    

    public function user(){
        return $this->belongsTo(User::class);
    }//end method user

    public function location(){
        return $this->morphOne(Location::class, "trackable");
    }//end method location

    public function watching(){
        return $this->morphMany(Watch::class, "watchable");
    }//end method watching

    public function documents(){
        return $this->morphMany(Document::class, "documentable");
    }//end method documents

    public function shares(){
        return $this->morphMany(Share::class, "shareable");
    }//end method shares

    public static function customPaginate($count = 20, $page = 1, $user){
        $total = static::whereDoesntHave("user", function($query) use (&$user){
                        $query->where("id", $user->id);
                    })->count();
        $perPage = $count;
        $currentPage = $page;
        $pageCount = ceil($total / $count);
        $firstPageUrl = url()->current() . "?count=$count&page=1";
        $lastPageUrl = url()->current() . "?count=$count&page=$pageCount";

        $prevPageUrl = $page > 1 ?  url()->current() . "?count=$count&page=" . ($page - 1) : null;
        $path =  url()->current();
        $from = 1;
        $to = $pageCount;
        

        $data = static::whereDoesntHave("user", function($query) use (&$user){
                    $query->where("id", $user->id);
                })
                ->with([
                    "comments" => function($query){
                        $query->with([
                            "document" => function($query2){
                                $query2->where("type", DocumentType::IMAGE)->first();
                            },
                            "comments" => function($query2){
                                $query2->with(["likes", "user:id,name,thumbnail"])->oldest()->take(1);
                            },
                            "likes", "user:id,name,thumbnail"
                        ])->oldest()->take(1);
                    },
                    "documents" => function($query){
                        $query->where("type", DocumentType::IMAGE)->orWhere("type", DocumentType::VIDEO);
                    },
                    "user" => function($query){
                        $query->select("id", "name", "thumbnail");
                    }
                    ])
                ->withCount(["likes", "comments", "watching", "shares"])
                ->skip($count * ($page - 1))
                ->take($count)
                ->orderBy("created_at", "desc")
                ->get();

                

        $data->each(function($post) use (&$user) {
            $post->liked = $post->likes->where("user_id", $user->id)->count() > 0 ? 1 : 0;

            $post->comments->each(function($comment) use (&$user){
                $comment->liked = $comment->likes->where("user_id", $user->id)->count() > 0 ? 1 : 0;
                $comment->likes_count = $comment->likes->count();
                unset($comment->likes);
    
                $comment->comments->each(function($c) use (&$user){
                    $c->liked = $c->likes->where("user_id", $user->id)->count() > 0 ? 1 : 0;
                    $c->likes_count = $c->likes->count();
                    unset($c->likes);
                });
            });
            unset($post->likes);
        });


        $returnCount = $data->count();

        return (object)[
            "total" => $total,
            "per_page" => $perPage,
            "current_page" => $page,
            "first_page_url" => $firstPageUrl,
            "last_page_url" => $lastPageUrl,
            "prev_page_url" => $prevPageUrl,
            "path" => $path,
            "from" => $from,
            "to" => $to,
            "data" => $data
        ];
    }//end method customPaginate
}//end class Post
