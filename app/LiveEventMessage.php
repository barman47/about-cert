<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class LiveEventMessage extends Model
{
    use UsesUuid;
    protected $guarded = [];

    public function liveEvent(){
        return $this->belongsTo(LiveEvent::class);
    }//end method liveEvent

    public function sender(){
        return $this->belongsTo(User::class, "sender_id");
    }//end method sender
}//end class LiveEventMessage
