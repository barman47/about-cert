<?php

namespace App\Http\Controllers;

use App\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "proficiency" => "required|integer|min:1|max:5"
        ]);

        $user = auth()->user();

        $temp = Skill::where([
            ["user_id", $user->id],
            ["name", $request->name]
        ])->first();

        if($temp == null)
            $user->skills()->create([
                "name" => $request->name,
                "proficiency" => $request->proficiency
            ]);

        return response()->json("OK");
    }//end method store
}
