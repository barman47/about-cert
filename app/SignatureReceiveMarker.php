<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class SignatureReceiveMarker extends Model
{
    use UsesUuid;

    protected $guarded = [];

    public function sendMarker(){
        return $this->belongsTo(SignatureSendMarker::class, "signature_send_marker_id");
    }//end method sendMarker

    public function receiver(){
        return $this->belongsTo(User::class, "receiver_id");
    }//end method receiver

    public function softDelete(){
        $this->deleted = 1;
        $this->save();
    }//end method softDelete
}//end class SignatureReceiveMarker
