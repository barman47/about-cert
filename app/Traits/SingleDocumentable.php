<?php

namespace App\Traits;
use App\Document;

trait SingleDocumentable{
    public function document(){
        return $this->morphOne(Document::class, "documentable");
    }//end method document
}//end class SingleDocumentable