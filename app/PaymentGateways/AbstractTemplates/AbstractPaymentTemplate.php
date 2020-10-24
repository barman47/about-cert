<?php
namespace App\PaymentGateways\AbstractTemplates;
use App\User;
use Illuminate\Http\Request;

use App\PaymentGateways\RaveGateway;

abstract class AbstractPaymentTemplate
{
    public static function getPaymentGatewayInstance(){
        $gateway = config("app_payment.gateway");
        if($gateway == "rave"){
            return new RaveGateway();
        }

        return new Exception("Invalid Payment Gateway.");
    }//end static method getPaymentInstance

    abstract function pay(float $amount, string $referenceId, User $user, $redirectUrl = null, $meta = null);
    abstract function requery(float $amount, string $referenceId): bool;
    abstract function webhook(Request $request);
}//end abstract class AbstractPaymentTemplate
