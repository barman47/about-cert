<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class Watch extends Model
{
    use UsesUuid;

    public function user(){
        return $this->belongsTo(User::class);
    }//end method user

    public function watchable(){
        return $this->morphTo();
    }//end method watchable

}//end class Watch
