<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;
use App\Interfaces\ShareableInterface;

class LiveEvent extends Model implements ShareableInterface
{
    use UsesUuid;
    protected $guarded = [];

    public function shares(){
        return $this->morphMany(Share::class, "shareable");
    }//end method shares

    public function user(){
        return $this->belongsTo(User::class);
    }//end method user


    public function messages(){
        return $this->hasMany(LiveEventMessage::class);
    }//end method messages
}//end class LiveEvent
