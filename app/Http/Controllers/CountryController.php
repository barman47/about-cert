<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Country;

class CountryController extends Controller
{
    public function index(){
        return response()->json(Country::orderBy("name")->select("name", "code")->get());
    }//end method index
}//end class CountryController
