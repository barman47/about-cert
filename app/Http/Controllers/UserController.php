<?php

namespace App\Http\Controllers;

use App\Exceptions\PostRepositoryException;
use App\Exceptions\UserRepositoryException;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function search()
    {
        $request = request();
        $request->validate([
            "query" => "required|string",
            "per_page" => "integer",
            "0" => "integer",
        ]);

        $userRepository = new UserRepository();

        $usersPaginationData = $userRepository->search($request->input("query"), $request->per_page ?? 10, $request->input("0") ?? 1);

        return response()->json($usersPaginationData, 200);
    } //end method search

    public function follow(Request $request)
    {
        $request->validate([
            "user_id" => "required|string",
        ]);

        $user = auth()->user();

        $userRepository = new UserRepository();

        $userRepository->user($user)->follow(User::findOrFail($request->user_id));

        return response()->json("OK", 201);
    } //end method folloe

    public function unfollow(Request $request)
    {
        $request->validate([
            "user_id" => "required|string",
        ]);

        $user = auth()->user();

        $userRepository = new UserRepository();

        $userRepository->user($user)->unfollow(User::findOrFail($request->user_id));

        return response()->json("OK", 204);
    } //end method unfollow

    public function show($id, UserRepository $userRepository)
    {
        try {
            $user = $userRepository->getUser($id);
        } catch (UserRepositoryException $e) {

            $message = $e->getMessage() ?? "An error occured";
            $code = $e->getCode() == 0 ? 400 : $e->getCode();

            return response()->json(["message" => $message], $code);
        }

        return response()->json(["user" => $user]);
    } //end method show
} //end class UserController
