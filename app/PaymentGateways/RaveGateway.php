<?php
namespace App\PaymentGateways;

use App\PaymentGateways\AbstractTemplates\AbstractPaymentTemplate;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

use Log;

use App\User;
use App\Transaction;

class RaveGateway extends AbstractPaymentTemplate
{
    private $meta;

    public function getMeta(){
        return $this->meta;
    }

    public function pay(float $amount, string $referenceId, User $user, $redirectUrl = null, $meta = null){
        $client = new Client();

        try{
            $response = $client->post("https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/hosted/pay", [
                "body" => json_encode([
                    'amount'=>$amount,
                    'customer_email'=>$user->email,
                    'currency'=>"NGN",
                    'txref'=>$referenceId,
                    'PBFPubKey'=>config("app_payment.rave.public_key"),
                    'redirect_url'=>$redirectUrl,
                    // 'payment_plan'=>$payment_plan
                    'meta'=> $meta
                  ]),
                "headers" => [
                    "Accept" => "application/json",
                    "Content-Type" => "application/json"
                ]
            ]);
        }catch(\Exception $e){
            // throw $e;
            return null;
        }

        $responseData = json_decode((string) $response->getBody());

        return $responseData->data->link;
    }//end method pay

    public function requery(float $amount, string $referenceId): bool{
        $client = new Client();

        // try{
            $response = $client->post("https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify", [
                "body" => json_encode([
                    'SECKEY'=>config("app_payment.rave.secret_key"),
                    "txref" => $referenceId
                  ]),
                "headers" => [
                    "Accept" => "application/json",
                    "Content-Type" => "application/json"
                ]
            ]);
        // }catch(\Exception $e){
        //     return false;
        // }

        $resp = json_decode((string) $response->getBody(), true);

        $paymentStatus = $resp['data']['status'];
        $chargeResponsecode = $resp['data']['chargecode'];
        $chargeAmount = $resp['data']['amount'];
        $chargeCurrency = $resp['data']['currency'];

        if (($chargeResponsecode == "00" || $chargeResponsecode == "0") && ($chargeAmount == $amount)) {
            $this->meta = $resp['data']['meta'];
          return true;
        } else {
            $this->meta = null;
            return false;
        }
    }//end method requery

    public function webhook(Request $request): Transaction {
        Log::info("Rave received =====================================================================>");

        $signature = (isset($_SERVER['HTTP_VERIF_HASH']) ? $_SERVER['HTTP_VERIF_HASH'] : '');

        if(!$signature)
            exit();

        $local_signature = config('app_payment.rave.secret_hash');

        if( $signature !== $local_signature ){
            // silently forget this ever happened
            Log::info("$signature !==  $local_signature");
            Log::info("Rave error =====================================================================>");
            exit();
        }

        http_response_code(200);
        Log::info("Rave success =====================================================================>");

        Log::info(json_encode($request->all()));

        $transaction = Transaction::where("reference_id", $request->input('txRef'))->first();

        // return ($this->requery($transaction->amount, $transaction->reference_id));
        return $transaction;


        // if($request->event == "charge.success"){
        //     $transaction = Transaction::where('reference_id', $request->input('data')['reference'])->first();

        //     if($transaction !== null){
        //         $transaction->status = TransactionStatus::SUCCESS;
        //         $transaction->save();
        //     }else{
        //         $this->createNewTransaction($request, TransactionType::DEPOSIT);
        //     }
        // }

        // if($request->event == "transfer.success"){
        //     $transaction = Transaction::where('transaction_id', $request->input('data')['reference'])->first();

        //     if($transaction !== null){
        //         $transaction->status = TransactionStatus::SUCCESS;
        //         $transaction->save();
        //     }
        // }

        // if($request->event == "transfer.failed"){
        //     $transaction = Transaction::where('transaction_id', $request->input('data')['reference'])->first();

        //     if($transaction !== null){
        //         $transaction->status = TransactionStatus::FAILED;
        //         $transaction->save();
        //     }
        // }
    }//end method webhook
}//end class RaveGatewayRaveGateway
