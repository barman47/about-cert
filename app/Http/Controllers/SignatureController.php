<?php

namespace App\Http\Controllers;

use App\Signature;
use Illuminate\Http\Request;

class SignatureController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $signatures = $user->signatures;

        $signatures->makeHidden("src");

        $signatures->each(function($signature){
            $signature->image = $this->convertToBase64($signature);
        });

        return response()->json($signatures);
    }//end method index

    public function store(Request $request)
    {
        $request->validate([
            "file" => "required|file|mimes:jpg,jpeg,png,svg|max:" . 1024 * 0.5,
            "name" => "required|string"
        ]);

        $user = auth()->user();

        $path = $request->file("file")->store("documents/signatures");

        $signature = $user->signatures()->create([
            "name" => $this->getFileName($user, $request->name),
            "src" => $path,
        ]);



        $signature->image = $this->convertToBase64($signature);

        unset($signature->src);

        return response()->json($signature, 201);
    }//end method store

    public function getFileName($user, $name){
        $count = 0;

        while(true){
            $found = false;

            foreach($user->certificates as $doc){
                if($doc->name == $name .($count == 0 ? "" : "($count)")){
                    $found = true;
                }
            }

            if(!$found){
                return $name . ($count == 0 ? "" : "($count)");
            }

            $count = $count + 1;
        }
    }//end method getFileName

    public function convertToBase64($signature){
        $path = storage_path() . "/app/" . $signature->src;
        $type = pathinfo($path, PATHINFO_EXTENSION);

        $base64 = "data:image/" . $type . ";base64," . base64_encode(file_get_contents($path));
        return $base64;
    }//end method convertToBase64
}
