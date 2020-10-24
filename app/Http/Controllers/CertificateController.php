<?php

namespace App\Http\Controllers;

use App\Certificate;
use App\Document;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CertificateController extends Controller
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
            "title"    => "string|required",
            "description"      => "string",
            "date"   => "required|date",
            "file"  => "required|mimes:jpeg,png,jpg"
        ]);

        $user = auth()->user();

        $certificate = $user->certificates()->create([
                                "title"    => $request->title,
                                "date"      => Carbon::create((string)$request->date),
                                "description"   => $request->description
                            ]);
        
        $path = $request->file("file")->store("documents/certificates");

        $document = new Document();

        $document  = $user->documents()->create([
            "documentable_id" => $certificate->id,
            "documentable_type" => Certificate::class,
            "src" => $path,
            "name" => $path
        ]);

        return response()->json($certificate);
    }//end method store

    public function getDocument(){
        $request = request();

        $request->validate([
            "id" => "required|string"
        ]);

        $user = auth()->user();

        $document = Document::where([
            ["documentable_type", Certificate::class],
            ["documentable_id", $request->id],
            ["user_id", $user->id]
        ])->first();

        if($document == null){
            return response()->json("Not Allowed", 422);
        }

        return response()->file(base_path() . "/storage/app/" . $document->src);

    }//end method getDocument

    /**
     * Display the specified resource.
     *
     * @param  \App\Certificate  $certificate
     * @return \Illuminate\Http\Response
     */
    public function show(Certificate $certificate)
    {
        //
    }//end mehod show

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Certificate  $certificate
     * @return \Illuminate\Http\Response
     */
    public function edit(Certificate $certificate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Certificate  $certificate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Certificate $certificate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Certificate  $certificate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Certificate $certificate)
    {
        //
    }
}
