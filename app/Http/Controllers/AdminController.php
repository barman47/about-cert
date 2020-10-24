<?php

namespace App\Http\Controllers;

use App\Exceptions\UserRepositoryException;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware("admin");
    } //end constructor method

    public function isAdmin()
    {
        return response()->json("OK", 200);
    } //end method isAdmin

    public function getUserProfileData(Request $request, UserRepository $userRepository)
    {
        $request->validate([
            "user_id" => "required|string",
        ]);

        try {
            $user = $userRepository->getUser($request->user_id);
        } catch (UserRepositoryException $e) {
            $message = $e->getMessage() ?? "An error Occured";
            $code = $e->getCode() < 400 ? 400 : $e->getCode();
            return response()->json(["message" => $message], $code);
        }

        return response()->json(["user" => $user], 200);
    } //end method getUserProfileData
} //end class AdminController
