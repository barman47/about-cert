<?php

namespace App\Http\Controllers;

use App\Language;
use Illuminate\Http\Request;

use App\UserLanguage;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Language::pluck("name"));
    }//end method index

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
        $request->validate([
            "name" => "required|string",
            "proficiency" => "required|integer|min:1|max:5"
        ]);

        $user = auth()->user();

        
        $interest = Language::where("name", $request->name)->first();

        if($interest == null){
            return response()->json("Language does not exist", 403);
        }

        $temp = UserLanguage::where([["user_id", $user->id], ["language_id", $interest->id]])->first();

        if($temp == null)   
            $user->languages()->attach($interest->id, ["proficiency" => $request->proficiency]);

        return response()->json("OK");
    }//end method store

    /**
     * Display the specified resource.
     *
     * @param  \App\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function show(Language $language)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function edit(Language $language)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Language $language)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function destroy(Language $language)
    {
        //
    }
}
