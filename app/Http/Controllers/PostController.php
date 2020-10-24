<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;

use App\User;
use Carbon\Carbon;
use App\DocumentType;
use App\Location;
use DB;

use App\Repositories\LocationRepository;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PostRepository $postRepository)
    {
        $request = request();
        $request->validate([
            "count" => "integer",
            "page" => "integer"
        ]);

        $user = auth()->user();

        $count = (int) $request->has("count") ? $request->input("count") : 20;
        $page = (int) $request->has("page") ? $request->input("page") : 1;

        // $posts = Post::customPaginate($count, $page, $user);
        $posts = $postRepository->user($user)->getPostsForIndexWithPagination();

        return response()->json($posts, 200);
    }//end method index

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "title" => "required|string",
            "content" => "required|string",
            "allow_comments" => "integer",
            "category" => "required|string",
            "time" => "required|string",
            "location" => "required|string",
            "img" => "image"
        ]);

        $user = auth()->user();

        $columns = $request->only(PostRepository::COLUMNS);

        $postRepository = new PostRepository();
        $postRepository->user($user)->jsonData($columns);

        if($request->has("img"))
            $postRepository->image($request->file("img"));

        $post = $postRepository->create();
        // return response()->file($postRepository->test());

        $locationRepository = new LocationRepository();
        $locationRepository->address($request->location)->attach($post);

        return response()->json($post->id, 201);
    }//end method store

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = auth()->user();

        $post = Post::where("id", $id)
                    ->with([
                        "location",
                        "comments" => function($query){
                            $query->with([
                                "document" => function($query2){
                                    $query2->where("type", DocumentType::IMAGE)->first();
                                },
                                "comments" => function($query2){
                                    $query2->with(["likes", "user:id,name,thumbnail"])->oldest()->take(10);
                                },
                                "likes", "user:id,name,thumbnail"
                            ])->withCount(["comments"])->latest()->take(10);
                        },
                        "documents" => function($query){
                            $query->where("type", DocumentType::IMAGE)->orWhere("type", DocumentType::VIDEO);
                        },
                        "user" => function($query){
                            $query->select("id", "name", "thumbnail");
                        }])
                    ->withCount(["likes", "comments", "watching", "shares"])
                    ->first();

        // $post->position = $post->location->position;
        // unset($post->location);

        $post->is_following_user = $user->id == $post->user->id ? 2 : ((new UserRepository())->user($user)->isFollowing($post->user) ? 1 : 0);

        $post->liked = $post->likes()->where("user_id", $user->id)->count() > 0 ? 1 : 0;
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

        return response()->json($post);
    }//end method show

    public function events(){
        $request = request();

        $request->validate([
            "count" => "integer",
            "page" => "integer"
        ]);
        $user = auth()->user();

        $count = $request->count ? $request->count : 20;
        $page = $request->page ? $request->page : 1;

        $total = $user->posts()->count();
        $perPage = $count;
        $currentPage = $page;
        $pageCount = ceil($total / $count);
        $firstPageUrl = rtrim(env("APP_URL"), "/") . "/events" . "/api?count=$count&page=1";
        $lastPageUrl = rtrim(env("APP_URL"), "/") . "/events" . "/api?count=$count&page=$pageCount";

        $prevPageUrl = $page > 1 ? rtrim(env("APP_URL"), "/") . "/events" . "/api?count=$count&page=" . ($page - 1) : null;
        $path = rtrim(env("APP_URL"), "/") . "/events";
        $from = 1;
        $to = $pageCount;


        $data = $user->posts()->with([
                    "documents" => function($query){
                        $query->where("type", DocumentType::IMAGE)->orWhere("type", DocumentType::VIDEO);
                    },
                    "user" => function($query){
                        $query->select("id", "name");
                    }
                    ])
                ->withCount(["likes", "comments", "watching"])
                ->skip($count * ($page - 1))
                ->take($count)
                ->orderBy("created_at", "desc")
                ->get();



        $data->each(function($post) use (&$user) {
            $post->liked = $post->likes->where("user_id", $user->id)->count() > 0 ? 1 : 0;
            unset($post->likes);
        });


        $returnCount = $data->count();

        return response()->json([
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
        ]);
    }//end method events

    public function otherUsersEvents($id, PostRepository $postRepository)
    {
        $user = User::where("id", $id)->first();

        if ($user == null) {
            return response()->json(["message" => "User does nnot exist"]);
        }

        try {
            $postsWithPagination = $postRepository->user($user)->getPostsByOtherUserWithPagination(null, ["content"]);
        } catch (PostRepositoryException $e) {
            $message = $e->getMessage() ?? "An error occured";
            $code = $e->getCode() == 0 ? 400 : $e->getCode();

            return response()->json(["message" => $message], $code);
        }

        $postsWithPagination->each(function($post) use (&$user) {
            $post->liked = $post->likes->where("user_id", $user->id)->count() > 0 ? 1 : 0;
            unset($post->likes);
        });

        return response()->json([
            "data" => $postsWithPagination,
        ]);
    } //end method otherUsersEvents

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $request->validate([
            "title" => "string",
            "content" => "string",
            "allow_comments" => "integer",
            "category" => "string",
            "time" => "string",
            "location" => "string",
        ]);

        $values = $request->only([
            "title",
            "content",
            "allow_comments",
            "category",
            "time"
        ]);

        DB::table("posts")->where("id", $id)->update($values);

        if($request->has("location")){
            DB::table("locations")->where([
                ["trackable_id", $id],
                ["trackable_type", Post::class]
            ])->update(["position" => $request->location]);
        }

        return response()->json("Ok");
    }//end method update

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
    }





    public function share(Request $request){
        $request->validate([
            "post_id" => "required|string",
            "receiver_ids" => "required|string"
        ]);

        $user = auth()->user();

        $post = Post::findOrFail($request->post_id);
        $receiver_ids = json_decode($request->receiver_ids, true);
        $postRepository = new PostRepository();

        $rv = false;

        if(count($receiver_ids) == 0)
            $rv = $postRepository->user($user)->post($post)->share($user);

        foreach($receiver_ids as $receiver_id){
            $rv = $postRepository->user($user)->post($post)->share(User::findOrFail($receiver_id));
        }

        if($rv == false)
            return response()->json("OK", 200);

        return response()->json("OK", 201);
    }//end method share
}//end class PostController
