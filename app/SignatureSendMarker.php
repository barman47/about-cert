<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class SignatureSendMarker extends Model
{
    use UsesUuid;

    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }// end method user

    public function document(){
        return $this->belongsTo(Document::class);
    }//end method document

    public function receiveMarkers(){
        return $this->hasMany(SignatureReceiveMarker::class);
    }//end method receiveMarkers

    public function softDelete(){
        $this->deleted = 1;
        $this->save();

        $this->receiveMarkers->each(function($marker){
            $marker->softDelete();
        });
    }//end method softDelete

    public function markExecuted(){
        $this->executed = 1;
        $this->save();

        $receiveSignatureMarkers = $this->receiveMarkers;
        $receiveSignatureMarkers->each(function($signatureReceiveMarker) {
            if($signatureReceiveMarker->signed == 0){
                $signatureReceiveMarker->signed = 1;
                $signatureReceiveMarker->save();
            }
        });
    }//end method markExecuted
}// end method SignatureSendMarker
