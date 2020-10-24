<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\JobTitle;

class JobTitleController extends Controller
{
    public function index(){
        return response()->json(JobTitle::all()->pluck("name"), 200);
    }//end method index
}//end class JobTitleController
