<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Exceptions\CommentRepositoryException;
use App\Repositories\CommentRepository;
use App\User;
use Illuminate\Http\Request;

class CommentController extends Controller
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
    public function store(Request $request, CommentRepository $commentRepository)
    {
        $request->validate([
            "id" => "required|string",
            "type" => "required|string",
            "content" => "required|string",
        ]);

        $user = auth()->user();

        try {
            $comment = $commentRepository->user($user)
                ->model($request->type, $request->id)
                ->content($request->content)
                ->create();
        } catch (CommentRepositoryException $e) {
            $message = $e->getMessage() ?? "An Error Occured";
            $code = $e->getCode() == 0 ? 400 : $e->getCode();

            return response()->json(["message" => $message], $code);
        }

        return response()->json(["comment" => $comment], 201);
    } //end method store

    /**
     * Display the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        //
    }
}
