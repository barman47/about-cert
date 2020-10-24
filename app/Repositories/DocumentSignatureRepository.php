<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

use Carbon\Carbon;

use App\Exceptions\DocumentSignatureRepositoryException;

use App\SignatureGateway\SignatureGatewayAbstractClass;
use App\User;
use App\Document;
use App\SignatureSendMarker;
use App\SignatureReceiveMarker;
use App\Priviledge;
use App\SignaturePriviledge;

use App\Bonanzas\Bonanza;

use App\Repositories\PriviledgeRepository;
use App\Repositories\DocumentRepository;

use App\Jobs\RevokePriviledge;

use App\Events\DocumentSendSignatureSignedEvent;
use App\Events\DocumentSendSignatureViewedEvent;
use App\Events\DocumentSendSignatureExecutedEvent;

use App\Events\DocumentReceiveSignatureViewedEvent;

use Log;
use Str;
use Storage;

class DocumentSignatureRepository{
    const PAYMENT_ID = "signature_priviledge";
    const SINGLE_DOCUMENT_PRIVILEDGE_CODE = "single_document_signature";
    const DAILY_DURATION_UNIT = "days";
    const WEEKLY_DURATION_UNIT = "weeks";
    const MONTHLY_DURATION_UNIT = "months";
    const ANNUAL_DURATION_UNIT = "years";
    const SIGNED_DOCUMENT_NAME_PREFIX = "Signed-";

    private $user;
    private $receivers;
    private $document;
    private $signatureGateway;


    public function __construct(SignatureGatewayAbstractClass $signatureGateway){
        $this->signatureGateway = $signatureGateway;
    }//end constructor method

    public function test(){
        // return $this->createSignatureRequest();
    }//end method test

    public function user(User $user) : DocumentSignatureRepository{
        $this->user = $user;
        return $this;
    }//end methos user

    public function receivers(Collection $users): DocumentSignatureRepository{
        $this->receivers = $users;
        return $this;
    }//end method receivers

    public function document(Document $document) : DocumentSignatureRepository{
        $this->document = $document;
        return $this;
    }//end method document

    public function webhook(Request $request){
        $returnVal = $this->signatureGateway->webhook($request);

        $signatureSendMarker = $returnVal[0];
        $data = $returnVal[1];
        $eventName = $data->event_name;

        if($eventName == "folder_sent")
            $this->webhookFolderSent($signatureSendMarker, $data);
        elseif($eventName == "folder_viewed")
            $this->webhookFolderViewed($signatureSendMarker, $data);
        elseif($eventName == "folder_signed")
            $this->webhookFolderSigned($signatureSendMarker, $data);
        elseif($eventName == "folder_cancelled")
            $this->webhookFolderCanceled($signatureSendMarker, $data);
        elseif($eventName == "folder_executed")
            $this->webhookFolderExecuted($signatureSendMarker, $data);
        elseif($eventName == "folder_deleted")
            $this->webhookFolderDeleted($signatureSendMarker, $data);
        Log::info("");
    }//end method webhook

    public function createSignatureRequest(){
        $hasBonanzaPriviledge = $this->hasBonanzaPriviledge();

        if(!$hasBonanzaPriviledge)
            if(!$this->hasPriviledgeToSign())
                throw new DocumentSignatureRepositoryException("The user doesn't have a priviledge", 322);

        $signatureSendMarker = new SignatureSendMarker();   
        $signatureSendMarker->document_id = $this->document->id;
        $signatureSendMarker->document_name = $this->document->name . ($this->document->extension ? ".{$this->document->extension}" : "");
        $signatureSendMarker = $this->user->signatureSendMarkers()->save($signatureSendMarker);

        $embeddedSessionURL = '';

        try{
            $file_url = \URL::temporarySignedRoute(
                'tempDocumentDownload', now()->addMinutes(5), ['document' => $this->document->id]
            );

            $data = [
                "signature_send_marker" => $signatureSendMarker,
                "receivers" => $this->receivers,
                "sender" => $this->user,
                "document" => $this->document,
                "file_url" => $file_url,
            ];

            $response = $this->signatureGateway->createSignatureRequest($data);
            $embeddedSessionURL = $response->embeddedSessionURL;
        }catch(\Exception $e){
            $signatureSendMarker->delete();
            throw $e;
        }


        $signatureSendMarker->sent = 0;
        $signatureSendMarker->data = serialize($response);
        $signatureSendMarker->embedded_signing_url = $embeddedSessionURL;
        $signatureSendMarker->save();

        if(!$hasBonanzaPriviledge)
            $this->revokePriviledgeIfNeeded($signatureSendMarker->user);

        return  ["embeddedSessionURL" => $embeddedSessionURL];
    }//end method requestForSignature

    public function revokePriviledgeIfNeeded(User $user){
        Log::info("");
        Log::info("<==============================Priviledge Action=============================>");
        Log::info(static::class . ": Revoke Priviledge if needed: User: {$user->email}; {$user->name}");

        $priviledgeRepository = new PriviledgeRepository;
        $priviledgeRepository->user($user);

        $userPriviledges = $user->priviledges()->where("code", "signature")->get();

        foreach($userPriviledges as $priviledge){
            $signaturePriviledge = SignaturePriviledge::find($priviledge->pivot->target_id);
            $priviledgeRepository->target($signaturePriviledge->id)->priviledge($priviledge);


            if($signaturePriviledge != null){
                if($signaturePriviledge->code == static::SINGLE_DOCUMENT_PRIVILEDGE_CODE){
                    $priviledgeRepository->destroy();
                    Log::info(static::class . ": Revoking single document signature priviledge");
                    break;
                }else{
                    $meta = $priviledgeRepository->getMeta();
                    $meta->count += 1;

                    if($meta->count >= $signaturePriviledge->max_within_duration){
                        //TODO: Alert User of deleted privilede
                        $priviledgeRepository->destroy();
                        Log::info(static::class . ": Priviledge Revoked");
                    }else{
                        //TODO: Alert User of remaining privilede
                        Log::info(static::class . " : Max : " . $signaturePriviledge->max_within_duration);
                        Log::info(static::class . " : Count : " .  $meta->count);
                        Log::info(static::class . " : Remaining : " . ($signaturePriviledge->max_within_duration - $meta->count));
                        $priviledgeRepository->meta($meta)->update();
                        Log::info(static::class . ": Priviledge Updated");
                    }
                }
            }
        }
        Log::info("");
    }//end method revokePriviledgeIfNeeded

    public function payForPriviledge(string $plan, string $redirectUrl): string{
        $plans = SignaturePriviledge::pluck("code")->toArray();

        if(!in_array($plan, $plans)){
            $rv = "Invalid plan '$plan': plan can only be of type ";

            foreach($plans as $p){
                $rv .= ", $p";
            }

            $rv .= ".";
            throw new DocumentSignatureRepositoryException($rv, 401);
        }

        $paymentRepository = new PaymentRepository();

        $user = auth()->user();

        $signaturePriviledge = SignaturePriviledge::where("code", $plan)->first();
        $amount = (float) $signaturePriviledge->price;

        $paymentRepository->user($user);

        $meta = [
            [
                "metaname" => "payment_for",
                "metavalue" => static::PAYMENT_ID
            ],
            [
                "metaname" => "target",
                "metavalue" => $signaturePriviledge->id
            ],
            [
                "metaname" => "user_id",
                "metavalue" => $user->id
            ]
        ];

        if(!$paymentRepository->makePayment($amount, $redirectUrl, $meta))
                throw new DocumentSignatureRepositoryException("An Error Occurred while trying to get payment link. Unable to make payment.", 501);

        return $paymentRepository->getPaymentLink();
    }//end method payFrPriviledge

    public function getSignatureReceiveMarkers(){
        $markers = $this->user
                    ->signatureReceiveMarkers()
                    ->with([
                        "sendMarker.user:name,id",
                        "receiver" => function($query){
                            $query->select("id", "name");
                        }])->where("deleted", 0)
                    ->latest()
                    ->paginate(10);
        $markers->each(function($marker){
            $marker->user_name = $marker->receiver->name;
            unset($marker->receiver);

            $marker->time = Carbon::create((string) $marker->created_at)->calendar();
            // $marker->time = Carbon::create((string) $marker->created_at)->format("Y-m-d H:i");
            
            $marker->sendMarker->recipients = $this->getSendMarkerRecipients($marker->sendMarker);
            unset($marker->sendMarker->data);
            unset($marker->sendMarker->embedded_signing_url);
        });

        $markers->makeHidden([
            "data"
        ]);

        return $markers;
    }//end method getSignatureReceiveMarkers

    public function getSignatureSendMarkers(){
        $markers = $this->user
                    ->signatureSendMarkers()
                    ->with(["receiveMarkers.receiver", "user" => function($query){
                        $query->select("id", "name");
                    }])->where("deleted", 0)
                    ->latest()
                    ->paginate(10);
        $markers->each(function($marker){
            $marker->user_name = $marker->user->name;

            unset($marker->user);
            
            $marker->recipients = $this->getSendMarkerRecipients($marker);
            $marker->time = Carbon::create((string) $marker->created_at)->calendar();
            // $marker->time = Carbon::create((string) $marker->created_at)->format("Y-m-d H:i");
        });

        $markers->makeHidden([
            "data"
        ]);

        return $markers;
    }//end method getSignatureSendMarkers

    public function paymentWebhook($meta){
        $templateGroup = null;
        $user = null;

        foreach($meta as $m2){
            if($m2["metaname"] == "target" && $m2["metavalue"])
                $signaturePriviledge = SignaturePriviledge::find($m2["metavalue"]);
            if($m2["metaname"] == "user_id" && $m2["metavalue"])
                $user = User::find($m2["metavalue"]);
        }

        $priviledgeRepository = new PriviledgeRepository();

        $priviledge = Priviledge::where("code", "signature")->first();
        $priviledgeRepository->target($signaturePriviledge->id)
                            ->user($user)
                            ->priviledge($priviledge)
                            ->meta(json_decode(json_encode(["count" => 0])))
                            ->create();
        Log::info("===>Priviledge granted to user: " . "{$user->id}:{$user->name}");

        if($signaturePriviledge->code != static::SINGLE_DOCUMENT_PRIVILEDGE_CODE){
            $duration = $signaturePriviledge->duration;
            $durationUnit = $signaturePriviledge->duration_unit;

            if($durationUnit == static::DAILY_DURATION_UNIT){
                RevokePriviledge::dispatch($user, $priviledge, $signaturePriviledge->id)->delay(now()->addDays($duration));
            }
            elseif($durationUnit == static::WEEKLY_DURATION_UNIT){
                RevokePriviledge::dispatch($user, $priviledge, $signaturePriviledge->id)->delay(now()->addWeeks($duration));
            }
            elseif($durationUnit == static::MONTHLY_DURATION_UNIT){
                RevokePriviledge::dispatch($user, $priviledge, $signaturePriviledge->id)->delay(now()->addMonths($duration));
            }
            elseif($durationUnit == static::ANNUAL_DURATION_UNIT){
                RevokePriviledge::dispatch($user, $priviledge, $signaturePriviledge->id)->delay(now()->addYears($duration));
            }
            Log::info("===> Priviledge expires in : {$duration} {$durationUnit}");
        }else{
            Log::info("===> Priviledge expires after first use");
        }

        Log::info("");
    }//end method paymentWebhook

    public function downloadSignedDocumentToFolder(SignatureSendMarker $signatureSendMarker, $folderId = null): Document{
        if($signatureSendMarker->user->id != $this->user->id && $signatureSendMarker->receiveMarkers()->where("receiver_id", $this->user->id)->first() == null)
            throw new DocumentSignatureRepositoryException("The user does't have the permission to access this file");

        $documentRepository = new DocumentRepository();
        $pathinfo = pathinfo($signatureSendMarker->document_name);
        $extension = pathinfo($signatureSendMarker->src)["extension"];

        $file = new \Illuminate\Http\File(storage_path("/app{$signatureSendMarker->src}"));

        return $documentRepository->user($this->user)
                            ->create(static::SIGNED_DOCUMENT_NAME_PREFIX . $pathinfo['filename'], $extension, "file", $file, $folderId);
    }// end method downloadSignedDocumentToFolder

    

    // ==============================================================
    // =                       PRIVATE FUNCTIONS                    =
    // ==============================================================

    private function getSendMarkerRecipients(SignatureSendMarker $signatureSendMarker) : array {
        $recipients = [];
        $onPlatformUsersEmails = [];

        $signatureSendMarker->receiveMarkers->each(function($marker) use (&$onPlatformUsersEmails){
            $onPlatformUsersEmails[] = strtolower($marker->receiver->email);
        });

        $receiveMarkers = collect($signatureSendMarker->receiveMarkers);

        $esignRecipients = unserialize($signatureSendMarker->data)->folder->folderRecipientParties;

        foreach($esignRecipients as $esr){
            $name = $esr->partyDetails->lastName . " " . $esr->partyDetails->firstName;
            $email = strtolower($esr->partyDetails->emailId);

            $isPlatformUser = in_array($email, $onPlatformUsersEmails);

            $recipient = [
                "name" => $name,
                "email" => $email,
                "id" => null,
                "is_platform_user" => 0,
                "signed" => 0,
                "viewed" => 0
            ];

            if($signatureSendMarker->sent == 1){
                if($isPlatformUser){
                    // Platform user
                    $temp = $receiveMarkers->first(function($value, $key) use (&$email){
                        return strtolower($value->receiver->email) == $email;
                    });

                    // var_dump($temp);
    
                    $recipient["is_platform_user"] = 1;
                    $recipient["id"] = $temp->receiver_id;
                    $recipient["signed"] = $temp->signed;
                    $recipient["viewed"] = $temp->viewed;
                }else {
                    // An external user
                }
            }//end if

            $signatureSendMarker->makeHidden("receiveMarkers");

            $recipients[] = $recipient;
        }//end foreach

        return $recipients;
    }//end method getSendMarkerRecipients

    private function hasBonanzaPriviledge(): bool{
        return (new Bonanza("signature", SignatureSendMarker::class))->hasPriviledge();
    }//end method hasBonanzaPriviledge

    private function hasPriviledgeToSign(): bool{
        if($this->user == null)
            throw new DocumentSignatureRepositoryException("The requesting user must be defined");

        $signaturePriviledges = SignaturePriviledge::all();

        $priviledgeRepository = new PriviledgeRepository();
        $priviledge = Priviledge::where([
            ["type", "signature"],
            ["code", "signature"]
        ])->first();

        $priviledgeRepository->user($this->user)->priviledge($priviledge);

        foreach($signaturePriviledges as $signaturePriviledge){
            $priviledgeRepository->target($signaturePriviledge->id);

            if($priviledgeRepository->hasPriviledge())
                return true;
        }

        return false;
    }//end method hasPriviledgeToSign

    private function webhookFolderSent(SignatureSendMarker $signatureSendMarker, $responseData = null){
        $data = \unserialize($signatureSendMarker->data);

        $signatureSendMarker->sent = 1;
        $signatureSendMarker->save();

        Log::info(static::class . ": Signature Request Sent");

        Log::info(static::class . ": Creating Receive Markers");

        $outsiders = array();

        foreach($data->folder->folderRecipientParties as $recipient){
            $temp_id = null;
            try{
                $temp_id = User::where("email", $recipient->partyDetails->emailId)->select("id")->first()->id;
            }catch(\Exception $e){

            }

            if($temp_id){
                $signatureSendMarker->receiveMarkers()->create([
                    "embedded_signing_url" => $recipient->folderAccessURL,
                    "receiver_id" => $temp_id,
                    "document_name" => $signatureSendMarker->document_name,
                    "data" => \serialize($recipient)
                ]);
            }else{
                $outsiders[] = [
                    "email" => $recipient->partyDetails->emailId,
                    "name" => "{$recipient->partyDetails->lastName} {$recipient->partyDetails->firstName}",
                    "viewed" => 0,
                    "signed" => 0
                ];
            }
        }//end foreach

        if(count($outsiders) > 0){
            $signatureSendMarker->outsiders_data = serialize(json_decode(json_encode($outsiders)));
            $signatureSendMarker->save();
        }

        Log::info(static::class . ": Receive Markers Created");
    }//end method webhookFolderSent

    private function webhookFolderViewed(SignatureSendMarker $signatureSendMarker, $responseData = null){
        Log::info(static::class . ": Signature Request Viewed by {$responseData->data->viewing_party->emailId}");

        $signatureReceiveMarker = $signatureSendMarker->receiveMarkers()->whereHas("receiver", function($query) use (&$responseData){
                                    // $query->where("email", "dayo@email.com");
                                    $query->where("email", $responseData->data->viewing_party->emailId);
                                })->first();

        if($signatureReceiveMarker == null){
            $outsiders = unserialize($signatureSendMarker->outsiders_data);

            for($i = 0; $i < count($outsiders); $i = $i){
                if($outsiders[$i]->email == $responseData->data->viewing_party->emailId){
                    $outsiders[$i]->viewed = 1;
                    break;
                }
            }

            $signatureSendMarker->outsiders_data = serialize($outsiders);
            $signatureSendMarker->save();
        }elseif($signatureReceiveMarker->viewed == 0){
            $signatureReceiveMarker->viewed = 1;
            $signatureReceiveMarker->save();
        }
        
        $email = $responseData->data->viewing_party->emailId;
        
        // broadcast(new DocumentReceiveSignatureViewedEvent($signatureSendMarker, $email));
        broadcast(new DocumentSendSignatureViewedEvent($signatureSendMarker, $email));

        Log::info(static::class . ": Signature viewed written");
    }//end method webhookFolderViewed

    private function webhookFolderSigned(SignatureSendMarker $signatureSendMarker, $responseData = null){
        Log::info(static::class . ": Signature Request Signed by {$responseData->data->signing_party->emailId}");

        $signatureReceiveMarker = $signatureSendMarker->receiveMarkers()->whereHas("receiver", function($query) use (&$responseData){
                                    // $query->where("email", "dayo@email.com");
                                    $query->where("email", $responseData->data->signing_party->emailId);
                                })->first();

        if($signatureReceiveMarker == null){
            $outsiders = unserialize($signatureSendMarker->outsiders_data);

            for($i = 0; $i < count($outsiders); $i = $i){
                if($outsiders[$i]->email == $responseData->data->signing_party->emailId){
                    $outsiders[$i]->signed = 1;
                    break;
                }
            }

            $signatureSendMarker->outsiders_data = serialize($outsiders);
            $signatureSendMarker->save();
        }elseif($signatureReceiveMarker->signed == 0){
            $signatureReceiveMarker->signed = 1;
            $signatureReceiveMarker->save();
        }

        $email = $responseData->data->signing_party->emailId;
        
        broadcast(new DocumentSendSignatureSignedEvent($signatureSendMarker, $email));

        Log::info(static::class . ": Signature signed written");
    }//end method webhookFolderSigned

    private function webhookFolderCanceled(SignatureSendMarker $signatureSendMarker, $responseData = null){
        Log::info(static::class . ": Signature Folder Canceled");
    }//end method webhookFolderCanceled

    private function webhookFolderDeleted(SignatureSendMarker $signatureSendMarker, $responseData = null){
        Log::info(static::class . ": Signature Folder Deleted");
    }//end method webhookFolderDeleted

    private function webhookFolderExecuted(SignatureSendMarker $signatureSendMarker, $responseData = null){
        Log::info(static::class . ": Signature Folder Executed");

        $userEmails = [];
        $documentRepository = new DocumentRepository();

        $resource = $this->signatureGateway->downloadFile($signatureSendMarker);

        $tempPath = "/documents/signed/" . Str::random(30) . ".pdf";
        Storage::put($tempPath , $resource);

        $signatureSendMarker->src = $tempPath;

        // $file = new \Illuminate\Http\File(storage_path("/app{$tempPath}"));

        // $documentPathInfo = pathinfo($signatureSendMarker->document_name);

        $folderRecipientParties = unserialize($signatureSendMarker->data)->folder->folderRecipientParties;
        foreach($folderRecipientParties as $party){
            $userEmails[] = $party->partyDetails->emailId;
        }

        //Add senders email
        $senderEmail = $signatureSendMarker->user->email;
        if(!in_array($senderEmail, $userEmails)){
            $userEmails[] = $senderEmail;
        }

        $users = User::whereIn("email", $userEmails)->get();

        // Log::info("copying executed file");
        Log::info(json_decode(json_encode($userEmails)));

        foreach($users as $user){
            // TODO: send notifications to the users
            // if(!$documentRepository->user($user)->validateSignedFolderExistence())
            //     continue;
    
            // $folderId = Document::where([
            //     ["user_id" , $user->id],
            //     ["name", DocumentRepository::SIGNED_FOLDER_NAME]
            // ])->select("id")->first()->id;
    
            // $document = $documentRepository->create($documentPathInfo["filename"], "pdf", "file", $file, $folderId);
        }

        // Log::info("copied");

        $signatureSendMarker->markExecuted();

        broadcast(new DocumentSendSignatureExecutedEvent($signatureSendMarker));

        Log::info("Document executed");
    }//end method webhookFolderExecuted

    
}//end class DocumentSignatureRepository
