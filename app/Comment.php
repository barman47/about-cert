<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;
use App\Traits\SingleDocumentable;
use App\Traits\Commentable;
use App\Traits\Likeable;

class Comment extends Model
{
    use UsesUuid, SingleDocumentable, Commentable, Likeable;
    
    protected $guarded = [];
    public static $commentableTypes = [
        "post",
        "comment"
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }//end method user

    public function commentable(){
        return $this->morphTo();
    }//end method commentable
}//end class Comment
