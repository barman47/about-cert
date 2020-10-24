<?php
namespace App\CVGenerators;

use App\PDFGenerators\PDFGenerator;

use App\Exceptions\CVGeneratorException;
use App\User;
use App\CVTemplate;


use Str;
use Storage;


class CVGenerator{
    private $user;
    private $template;

    public function run(string $inputPath = null): string{
        $fullPath = "";
        $user = $this->user;
        $template = $this->template;

        if(!file_exists(storage_path("app/cvs"))){
            mkdir(storage_path("app/cvs"), 0777, true);
        }

        $folder = "app/cvs/{$user->username}/";
        
        if(!file_exists(storage_path($folder))){
            mkdir(storage_path($folder), 0777, true);
        }
        
        if($inputPath){
            $fullPath = $inputPath;

            if(!file_exists(storage_path($fullPath))){
                $fullPath = $folder . Str::random(30) . ".pdf";
            }
        }else{
            $fullPath = $folder . Str::random(30) . ".pdf";
        }

        $storageFullPath = "../storage/" . $fullPath;

        $url = \URL::temporarySignedRoute(
            'serveCVViewForPDFConversion', now()->addMinutes(15), [
                    'user' => $user->id,
                    'template' => $template->id
                ]
        );
        // $url = url("/pdf/view");
        //
        
        $pdfGenerator = new PDFGenerator();
        $pdfGenerator->url($url)
                    ->path($storageFullPath)
                    ->run();
                    
        return $fullPath;
    }//end method run

    public function user(User $user): CVGenerator{
        $this->user = $user;
        return $this;
    }//end method user

    public function template(CVTemplate $template): CVGenerator{
        $this->template = $template;
        return $this;
    }//end method template
}//end class CVGenerator