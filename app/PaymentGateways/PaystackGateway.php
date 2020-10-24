<?php
namespace App\PaymentGateways;

use App\PaymentGateways\AbstractTemplates\AbstractPaymentTemplate;

class PaystackGateway extends AbstractPaymentTemplate
{
    public function pay(float $amount, string $referenceId): bool{
        return true;
    }//end method pay

    public function requery(float $amount, string $referenceId): bool{
        return true;
    }//end method requery
}//end class PaystackGateway
