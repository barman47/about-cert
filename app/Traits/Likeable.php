<?php

namespace App\Traits;

use App\Like;

trait Likeable{
    public function likes(){
        return $this->morphMany(Like::class, "likeable");
    }//end method likes
}//end trait Commentable