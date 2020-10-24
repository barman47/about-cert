<?php

namespace App\SignatureGateway;
use Illuminate\Http\Request;
use App\SignatureSendMarker;

abstract class SignatureGatewayAbstractClass{
    public static function getGatewayInstance(){
        $gateway = config("app_signature.gateway");

        if($gateway == "esign_genie"){
            return new ESignGenieGateway();
        }elseif($gateway == "hellosign"){
            return new HelloSignGateway();
        }

        throw new \Exception("Invalid Gateway");
    }//end static method getGatewayInstance

    public function webhook(Request $request){}//end method webhook

    public abstract function getSignUrl();

    public abstract function createSignatureRequest($object = null);

    public abstract function downloadFile(SignatureSendMarker $signatureSendMarker);
}//end interface SignatureGatewayAbstractClass
