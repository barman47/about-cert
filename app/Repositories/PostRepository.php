<?php
namespace App\Repositories;

use App\DocumentType;
use App\Events\PostCreatedEvent;
use App\Exceptions\PostRepositoryException;
use App\Handlers\Alerts\PostHandler;
use App\Jobs\ResizePostPhoto;
use App\Post;
use App\Repositories\AlertRepository;
use App\User;
use Carbon\Carbon;
use Storage;

class PostRepository
{
    const COLUMNS = [
        "title",
        "content",
        "allow_comments",
        "category",
        "time",
    ];
    private $user;
    private $post;

    private $image;
    private $video;

    private $defaultColumns = [
        "allow_comments" => 1,
    ];

    public function __construct()
    {
        $this->post = new Post();
    } //end constructor

    public function post(Post $post): PostRepository
    {
        $this->post = $post;

        return $this;
    } //end method post

    public function user(User $user): PostRepository
    {
        $this->user = $user;

        return $this;
    } //end method user

    public function getPostsByOtherUserWithPagination($columns = null, $exclude = null)
    {
        if (!$this->user) {
            return new PostRepositoryException("The user object must be defined");
        }

        $builder = $this->user->posts();

        if (gettype($columns) == "array") {
            $builder = $builder->select(implode(",", $columns));
        } elseif (gettype($columns) == "string") {
            $builder = $builder->select($columns);
        } elseif (gettype($exclude) == "array") {
            $builder = $builder->exclude($exclude);
        } elseif (gettype($exclude) == "string") {
            $builder = $builder->exclude([$exclude]);
        }

        $builder->with([
            "comments" => function ($query) {
                $query->with([
                    "document" => function ($query2) {
                        $query2->where("type", DocumentType::IMAGE)->first();
                    },
                    "comments" => function ($query2) {
                        $query2->with(["likes", "user:id,name,thumbnail"])->oldest()->take(1);
                    },
                    "likes", "user:id,name,thumbnail",
                ])->oldest()->take(1);
            },
            "documents" => function ($query) {
                $query->where("type", DocumentType::IMAGE)->orWhere("type", DocumentType::VIDEO);
            },
            "user" => function ($query) {
                $query->select("id", "name", "thumbnail");
            },
        ])->withCount(["likes", "comments", "watching", "shares"]);

        return $builder->latest()->paginate();
    } //end method getPosts

    public function getPostsForIndexWithPagination()
    {
        $user = $this->user;

        $posts = Post::whereDoesntHave("user", function ($query) use (&$user) {
            $query->where("id", $user->id);
        })
            ->with([
                "documents" => function ($query) {
                    $query->where("type", DocumentType::IMAGE)->orWhere("type", DocumentType::VIDEO);
                },
                "user" => function ($query) {
                    $query->select("id", "name", "thumbnail");
                },
            ])
            ->withCount(["likes", "comments", "watching", "shares"])
            ->latest()
            ->paginate(30);
        
        $posts->each(function($post) use ($user){
            $post->formatted_time = Carbon::create((string) $post->time)->format("D, M d, Y");
            $post->liked = $post->likes->where("user_id", $user->id)->count() > 0 ? 1 : 0;
            $post->formatted_created_at = Carbon::create((string) $post->created_at)->diffForHumans();
            unset($post->likes);
        });

        return $posts;
    } //end method getPostsForIndex

    public function jsonData($data): PostRepository
    {
        foreach ($data as $k => $v) {
            $this->post->{$k} = $v;
        }

        return $this;
    } //end method postData

    public function image($img): PostRepository
    {
        $this->image = $img;
        return $this;
    } //end method image

    public function share(User $user, Post $post = null): bool
    {
        $post = $post ?? $this->post;

        if (!$this->user) {
            return new PostRepositoryException("The sending user must be specified");
        }

        if (!$user) {
            return new PostRepositoryException("The receiving user must be specified");
        }

        $sharingRepository = new SharingRepository();

        $rv = false;

        if ($sharingRepository->user($this->user)->shareable($post)->create()) {
            if ($user->id != $this->user->id) {
                $alertRepository = new AlertRepository();

                $alertRepository
                    ->sender($this->user)
                    ->receiver($user)
                    ->data($post)
                    ->handler(new PostHandler())
                    ->create();
            }

            $rv = true;
        }

        return $rv;
    } //end method share

    public function create(): Post
    {
        if (!$this->user) {
            return new PostRepositoryException("The user object must be defined");
        }

        if ($this->image) {
            $this->post->img = Storage::url(Storage::disk("public")->put("post_photos", $this->image));
        }

        foreach ($this->defaultColumns as $k => $v) {
            $this->post->{$k} = $this->post->{$k} ?? $v;
        }

        $this->user->posts()->save($this->post);

        if ($this->image) {
            $this->resizePhoto();
        }

        broadcast(new PostCreatedEvent($this->post))->toOthers();
        return $this->post;
    } //end method create

    private function resizePhoto()
    {
        ResizePostPhoto::dispatch($this->post);
    } //end mehtod resizePhoto
} //end class PostRepository
