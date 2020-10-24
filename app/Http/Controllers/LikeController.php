<?php

namespace App\Http\Controllers;

use App\Like;
use App\Post;
use App\User;
use App\Comment;
use App\Opportunity;
use Illuminate\Http\Request;

use App\Repositories\LikeRepository;
use App\Exceptions\LikeRepositoryException;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, LikeRepository $likeRepository)
    {
        $request->validate([
            "id" => "required|string",
            "type" => "required|string"
        ]);

        $user = auth()->user();
        
        try{
            if($likeRepository->user($user)->id($request->id)->type($request->type)->commit())
                return response()->json("liked", 201);
            else
                return response()->json("unliked", 202);
        }catch(LikeRepositoryException $e){
            return response()->json([
                "message" => $e->getMessage() ?? "An error occured",
            ],$e->getCode() != 0 ? $e->getCode() : 400);
        }
    }//end method store

    /**
     * Display the specified resource.
     *
     * @param  \App\Like  $like
     * @return \Illuminate\Http\Response
     */
    public function show(Like $like)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Like  $like
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Like $like)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Like  $like
     * @return \Illuminate\Http\Response
     */
    public function destroy(Like $like)
    {
        //
    }
}
