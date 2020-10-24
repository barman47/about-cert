<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use App\PaymentGateways\AbstractTemplates\AbstractPaymentTemplate;
use App\PaymentGateways\RaveGateway;

use App\User;
use App\CVTemplateGroup;
use App\Priviledge;
use App\Repositories\PriviledgeRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\DocumentSignatureRepository;
use App\SignatureGateway\SignatureGatewayAbstractClass;

use App\Repositories\AlertRepository;
use App\Repositories\CVRepository;
use App\Jobs\RevokePriviledge;

class PaymentController extends Controller
{
    private $paymentGateway;

    public function __construct(){

    }

    public function webhook(Request $request,string $gateway){
        Log::info("payment received");

        $paymentRepository = new PaymentRepository();

        if(!$paymentRepository->webhook($request)) exit();

        $meta = $paymentRepository->getPaymentGatewayMetaData();

        if(gettype($meta) == "array" ||
            gettype($meta) == "object"
        ){
            foreach($meta as $m){

                //==> Template Group
                if($m["metaname"] == "payment_for" && $m["metavalue"] == "template_group"){
                    Log::info("================ CV Template Group Payment============================>");
                    $repo = new CVRepository();
                    $repo->paymentWebhook($meta);                    
                }
                
                //==> Tailored Templates
                elseif($m["metaname"] == "payment_for" && $m["metavalue"] == "tailored_template"){
                    Log::info("================ CV Template Group Payment============================>");
                    $repo = new CVRepository();
                    $repo->paymentWebhookForTailoredCV($meta);
                }
                
                //==> Document Signature
                elseif($m["metaname"] == "payment_for" && $m["metavalue"] == DocumentSignatureRepository::PAYMENT_ID){
                    Log::info("================Document Signature Payment============================>");
                    $repo = new DocumentSignatureRepository(SignatureGatewayAbstractClass::getGatewayInstance());
                    $repo->paymentWebhook($meta);
                }
            }
        }
    }//end method raveIndex
}//end class PaymentController
