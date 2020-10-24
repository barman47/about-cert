<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Traits\UsesUuid;
use App\Traits\SingleDocumentable;

class Certificate extends Model
{
    use UsesUuid, SingleDocumentable;
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }//end method user

    public function document(){
        return $this->morphOne(Document::class, "documentable");
    }//end method deocument
}//end class Certificate
