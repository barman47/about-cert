<?php

namespace App\Traits;

use App\Document;

trait MultipleDocumentable{
    public function documents(){
        return $this->morphMany(Document::class, "documentable");
    }//end method documents
}//end class MultipleDocumentable