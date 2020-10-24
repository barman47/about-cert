<?php

namespace App\Http\Controllers;

use App\WorkExperience;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WorkExperienceController extends Controller
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
            "start_date"        => "date|required",
            "end_date"          => "required|string",
            "description"       => "required|string",
            "company_name"      => "required|string",
            "position"          => "required|string",
            "achievements"      => "string"
        ]);

        $user = auth()->user();

        $workExperience = $user->workExperiences()->create([
                                "start_date"    => Carbon::create((string) $request->start_date),
                                "end_date"      => $request->end_date,
                                "work_description"   => $request->description,
                                "company_name"      => $request->company_name,
                                "position"          => $request->position,
                                "achievements"      => $request->achievements
                            ]);

        return response()->json($workExperience);
    }//end method store

    /**
     * Display the specified resource.
     *
     * @param  \App\WorkExperience  $workExperience
     * @return \Illuminate\Http\Response
     */
    public function show(WorkExperience $workExperience)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\WorkExperience  $workExperience
     * @return \Illuminate\Http\Response
     */
    public function edit(WorkExperience $workExperience)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\WorkExperience  $workExperience
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WorkExperience $workExperience)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\WorkExperience  $workExperience
     * @return \Illuminate\Http\Response
     */
    public function destroy(WorkExperience $workExperience)
    {
        //
    }
}
