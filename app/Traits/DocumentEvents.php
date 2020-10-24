<?php
namespace App\Traits;
use App\Document;
use App\DocumentType;

use Storage;

trait DocumentEvents{
    protected static function bootDocumentEvents(){
        static::creating(function($model){
            $imageExtensions = [
                "jpg",
                "jpeg",
                "png",
                "gif",
                "tiff",
                "psd",
                "ai",
                "svg"
            ];

            $fileExtensions = [
                "pdf",
                "doc",
                "txt",
                "html",
                "yml",
                "md",
                "js",
                "html",
                "css",
                "java",
                "json",
                "ts",
                "cpp",
                "xml",
                "bash",
                "less",
                "nginx",
                "php",
                "powershell",
                "python",
                "scss",
                "shell",
                "sql",
                "yaml",
                "ini",

                //Office
                "docm",
                "dotx",
                "dotm",
                "docb",
                "docb",
                "xls",
                "xlt",
                "xlm",
                "xlsx",
                "xltx",
                "xltm",
                "xlsb",
                "xla",
                "xlam",
                "xll",
                "xlw",
                "ppt",
                "pot",
                "pps",
                "pptx",
                "pptm",
                "potx",
                "potm",
                "potx",
                "ppam",
                "ppsx",
                "ppsm",
                "sldx",
                "sldm"
            ];

            if($model->type != DocumentType::FOLDER){
                $explode = explode(".", $model->src);
                $extension = $explode[count($explode) - 1];

                if(!$model->extension)
                    $model->extension = $extension;

                if(!$model->size)
                    $model->size = number_format( (float) (Storage::size($model->src) / 1024 / 1024), 2) . " MB";

                if(!$model->type){
                    if(in_array($extension, $imageExtensions))
                        $model->type = DocumentType::IMAGE;
                    else
                        $model->type = DocumentType::FILE;
                }
            }// end if not folder
            else{
                $model->src = "";
                $model->extension = "";
                $model->size = "";
            }
        });
    }//end boot method
}//end trait DocumentEvents
