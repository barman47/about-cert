<?php

namespace App\Http\Controllers;

use App\AcademicDetail;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AcademicDetailsController extends Controller
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
            "start_date"    => "date|required",
            "end_date"      => "required|string",
            "institution"   => "required|string",
            "location"      => "required|string"
        ]);

        $user = auth()->user();

        $academicDetail = $user->academicDetails()->create([
                                "start_date"    => Carbon::create((string) $request->start_date),
                                "end_date"      => $request->end_date,
                                "institution"   => $request->institution,
                                "location"      => $request->location
                            ]);

        return response()->json($academicDetail->id);
    }//end method store

    /**
     * Display the specified resource.
     *
     * @param  \App\AcademicDetail  $academicDetail
     * @return \Illuminate\Http\Response
     */
    public function show(AcademicDetail $academicDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AcademicDetail  $academicDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(AcademicDetail $academicDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AcademicDetail  $academicDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AcademicDetail $academicDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AcademicDetail  $academicDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicDetail $academicDetail)
    {
        //
    }
}
