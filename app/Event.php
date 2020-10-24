<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;
use App\Traits\MultipleDocumentable;
use App\Traits\Commentable;
use App\Traits\Likeable;

class Event extends Model
{
    use UsesUuid, MultipleDocumentable, Commentable, Likeable;

    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }//end method user
}//end class Event
