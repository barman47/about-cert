<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

use App\Repositories\UserRepository;
use App\Exceptions\UserRepositoryException;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UserRepository $userRepository)
    {
        $user = auth()->user();

        try {
            $data = $userRepository->user($user)->getProfileData(true);
        } catch (UserRepositoryException $e) {
            $message = $e->getMessage() ?? "An error occured";
            $code = $e->getCode() == 0 ? 400 : $e->getCode();
            return response()->json(["message" => $message], $code);
        }
        
        
        return response()->json($data);
    }//end method index

    public function otherOtherUserProfile($id, UserRepository $userRepository){
        $user = User::where("id", $id)->first();

        if($user == null){
            return response()->json(["message" => "User does not exist"], 404);
        }

        try {
            $data = $userRepository->user($user)->getProfileData(false);
        } catch (UserRepositoryException $e) {
            $message = $e->getMessage() ?? "An error occured";
            $code = $e->getCode() == 0 ? 400 : $e->getCode();
            return response()->json(["message" => $message], $code);
        }
        
        
        return response()->json($data);
    }//end method otherOtherUserProfile

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
