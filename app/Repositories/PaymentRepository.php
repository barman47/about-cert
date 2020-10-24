<?php
namespace App\Repositories;

use Illuminate\Support\Str;

use App\PaymentGateways\AbstractTemplates\AbstractPaymentTemplate;
use App\PaymentGateways\PaystackGateway;
use App\PaymentGateways\RaveGateway;

use App\Exceptions\PaymentRepositoryException;

use App\Transaction;
use App\TransactionStatus;

use App\User;

class PaymentRepository{
    private $paymentGateway;
    private $user;
    private $paymentLink;

    public function __construct(){
        $this->paymentGateway = AbstractPaymentTemplate::getPaymentGatewayInstance();
    }//end constructor

    public function getPaymentLink(){
        return $this->paymentLink;
    }//end method getPaymentLink

    public function user(User $user): PaymentRepository
    {
        $this->user = $user;
        return $this;
    }//end method user

    public function makePayment(float $amount, string $redirectUrl = null, $meta = null): bool
    {
        if($this->user == null || $this->paymentGateway == null)
            throw new PaymentRepositoryException("The user object is not defined or the payment gateway is missing");

        $referenceId = $this->user->username . "-" . Str::random(30);

        $transaction = $this->user->transactions()->create([
                            "amount" => $amount,
                            "status" => TransactionStatus::PENDING,
                            "reference_id" => $referenceId
                        ]);

        $redirectLink = $this->paymentGateway->pay($amount, $referenceId, $this->user, $redirectUrl, $meta);

        $this->paymentLink = $redirectLink;

        if($redirectLink)
            return true;
        return false;

        // if($paid && $this->requery($amount, $referenceId)){
        //     return true;
        // }else{
        //     return false;
        // }

        // return true;
    }//end method makePayment

    public function requery(float $amount, string $referenceId): bool
    {
        if($this->paymentGateway == null)
            throw new PaymentRepositoryException("The payment gateway is missing");


        $transaction = null;

        if($this->user){
            $transaction = $this->user->transactions()->where("reference_id", $referenceId)->first();
            if($transaction == null)
                $transaction = $this->user->transactions()->create([
                                "amount" => $amount,
                                "status" => TransactionStatus::PENDING,
                                "reference_id" => $referenceId
                            ]);
        }else{
            $transaction = Transaction::where("reference_id", $referenceId)->first();
            if($transaction == null){
                throw new PaymentRepositoryException("The user is not defined: Therefore default transaction cannot be created");
            }
        }

        $paid = $this->paymentGateway->requery($amount, $referenceId);

        if($paid){
            $transaction->status = TransactionStatus::SUCCESS;
            $transaction->save();
        }else{
            $transaction->status = TransactionStatus::FAILED;
            $transaction->save();

            return false;
        }

        return true;
    }//end method requeryPayment

    public function webhook($request) : bool{
        $transaction = $this->paymentGateway->webhook($request);

        return $this->requery($transaction->amount, $transaction->reference_id);
    }//end method webhook

    public function getPaymentGatewayMetaData(){
        return $this->paymentGateway->getMeta();
    }//end method getPaymentGatewayMetaData
}//end class PaymentRepository
