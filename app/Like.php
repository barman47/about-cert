<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Traits\UsesUuid;

class Like extends Model
{
    use UsesUuid;
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }//end method user

    public function likeable(){
        return $this->morphTo();
    }//end method likable
}//end class Like
