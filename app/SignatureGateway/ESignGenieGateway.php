<?php
namespace App\SignatureGateway;
use Illuminate\Http\Request;

use GuzzleHttp\Client;

use Log;
use Str;
use Storage;

use App\Repositories\AlertRepository;
use App\SignatureSendMarker;


class ESignGenieGateway extends SignatureGatewayAbstractClass{
    const BASE_URL = "https://www.esigngenie.com/esign/api";
    const GENERATE_ACCESS_TOKEN_END_POINT = "/oauth2/access_token";
    const CREATE_FOLDER_END_POINT = "/folders/createfolder";
    const DOWNLOAD_DOCUMENT_END_POINT = "/folders/document/download";

    private $accessToken;
    private $clientId;
    private $clientSecret;
    private $apiHash;
    private $alertRepository;

    public function __construct(){
        $this->clientId = config('app_signature.esign_genie.api_key');
        $this->clientSecret = config('app_signature.esign_genie.api_secret');
        $this->apiHash = config('app_signature.esign_genie.api_hash');
        $this->alertRepository = new AlertRepository();
    }//end constructor

    private function generateAccessToken(){
        $client = new Client();

        $response = $client->post(static::BASE_URL . static::GENERATE_ACCESS_TOKEN_END_POINT, [
            "form_params" => [
                'grant_type' => "client_credentials",
                "client_id" => "$this->clientId",
                "client_secret" => $this->clientSecret,
                "scope" => "read-write"
            ],
            "headers" => [
                "Accept" => "application/json",
                "Content-Type" => "application/x-www-form-urlencoded",
            ]
        ]);

        $resp = json_decode((string) $response->getBody());

        $this->accessToken = $resp->access_token;
    }//end method generateAccessToken

    private function getAccessToken(){
        if(!$this->accessToken)
            $this->generateAccessToken();
        return $this->accessToken;
    }//end method getAccessToken

    public function createSignatureRequest($object = null){
        if($object == null)
            return new \Exception("The object must be specified");

        $document = $object["document"];
        $receivers = $object["receivers"];
        $sender = $object["sender"];
        $fileUrl = $object["file_url"];
        $signatureSendMarker = $object["signature_send_marker"];

        $parties = array();
        $baseUrl = rtrim(config("web_client_details.url"), "/") . "/documents";

        foreach($receivers as $index => $receiver){
            //  Permmissions: FILL_FIELDS_AND_SIGN, FILL_FIELDS_ONLY, SIGN_ONLY, VIEW_ONLY
            $names = explode(" ", preg_replace('/\s+/i', " ", $receiver->name));

            $firstName = count($names) > 1 ? $names[1] :  "";
            $lastName = $names[0];

            $parties[] = [
                "firstName" => $firstName,
                "lastName" => $lastName,
                "emailId" => $receiver->email,
                "permission" => "FILL_FIELDS_AND_SIGN",
                "sequence" => $index + 1,
                "allowNameChange" => true
            ];
        }

        $fields = [];

        // for($i = 1; $i <= count($parties); $i = $i + 1){
        //     $fileds[] = [
        //         "type" => "signature",
        //         "tooltip" => $parties[$i - 1]["firstName"] . " " . $parties[$i - 1]["firstName"],
        //         "required" =>true,
        //         "x" => 150,
        //         "y" => 100,
        //         "width" => 200,
        //         "height" => 200,
        //         "documentNumber" => "1",
        //         "pageNumber" => "1"
        //     ];
        // }

        $data = [
            "folderName" => $document->name . "-" . Str::random(30),
            "fileUrls" => [$fileUrl],
            "fileNames" => ["{$document->name}.{$document->extension}"],
            // "processTextTags" => true,
            // "processAcroFields" => true,
            "signInSequence" => false,
            "inPersonEnable" => false,
            "custom_field1" => [
                "name" => "signature_send_marker",
                "value" => $signatureSendMarker->id
            ],
            // "custom_field2" => [
            //     "name" => "signature_send_marker",
            //     "value" => $signatureSendMarker->id
            // ],
            "sendNow" => true,
            // "emailTemplateId" => 2,
            // "emailTemplateCustomFields" => [
            //     [
            //         "tag" => "CUSTOM_TAGS_!",
            //         "type" => "plain",
            //         "value" => "Please click on the View Document(s) link below to review and esign the docum"
            //     ]
            //  ],
            "createEmbeddedSendingSession" => true,
            "fixRecipientParties" => true,
            "fixDocuments" => true,
            //TODO: edit the next line
            "sendSuccessUrl" => $baseUrl,
            //TODO: edit the next line
            "sendErrorUrl" => $baseUrl,
            "createEmbeddedSigningSession" => true,
            "createEmbeddedSigningSessionForAllParties" => true,
            "embeddedSignersEmailIds" => $receivers->pluck("email"),
            "signSuccessUrl" => $baseUrl,
            "signDeclineUrl" => $baseUrl,
            "signLaterUrl" => $baseUrl,
            "signErrorUrl" => $baseUrl,
            "themeColor" => "#0084ff",
            "parties" => $parties,
            "fields" => $fields,
            // "dependentFields" => [
            //     [

            //     ]
            //     ],
            "hideDeclineToSign" => true,
            "hideAddPartiesOption" => true,
            //TODO: uncomment the next line and comment the one after
            // "senderEmail" => $sender->email,
            "senderEmail" => "davexoyinbo@gmail.com",
        ];

        $client = new Client();

        $response = $client->post(static::BASE_URL . static::CREATE_FOLDER_END_POINT, [
            "body" => json_encode($data),
            "headers" => [
                "Authorization" => "Bearer " . $this->getAccessToken(),
                "Content-Type" => "application/json",
                // "Accept" => "application/json"
            ]
        ]);

        $responseJson = json_decode((string) $response->getBody());

        if($responseJson->result && $responseJson->result == "error")
            throw new \Exception($responseJson->error_description);

        return $responseJson;
    }//end method createSignatureRequest

    public function webhook(Request $request): array{
        Log::info("==================Signature Webhook===================");

        $request_body = file_get_contents('php://input');
        $s = hash_hmac('sha256', $request_body, $this->apiHash, true);
        Log::info(base64_encode($s));

        Log::info($request->all()['signature']);
        
        if(($request->all()['signature'] != base64_encode($s))){
            Log::info("===============> EsignGenie Webhook Signatures not equal");
            exit();
        }
        
        http_response_code(200);
        
        $data = json_decode(json_encode($request->all()));

        Log::info(json_encode($data));

        if($data->event_name == "folder_sent")
            $this->webhookFolderSent();
        elseif($data->event_name == "folder_viewed")
            $this->webhookFolderViewed();
        elseif($data->event_name == "folder_signed")
            $this->webhookFolderSigned();
        elseif($data->event_name == "folder_cancelled")
            $this->webhookFolderCanceled();
        elseif($data->event_name == "folder_executed")
            $this->webhookFolderExecuted();
        elseif($data->event_name == "folder_deleted")
            $this->webhookFolderDeleted();

        return [SignatureSendMarker::findOrFail($data->data->folder->custom_field1->value), $data];
    }//end method webhook

    public function downloadFile(SignatureSendMarker $signatureSendMarker){
        $client = new Client();

        $folderId = json_decode(json_encode(unserialize($signatureSendMarker->data)))->folder->folderId;

        $response = $client->get(static::BASE_URL . static::DOWNLOAD_DOCUMENT_END_POINT . "?folderId={$folderId}&docNumber=1"  , [
            "headers" => [
                "Accept" => "application/pdf",
                "Authorization" => "Bearer " . $this->getAccessToken()
            ]
        ]);

        return $response->getBody();
    }//end method getfiles

    private function webhookFolderSent($data = null){
        Log::info(static::class . ": Signature Request Sent");
    }//end method webhookFolderSent

    private function webhookFolderViewed($data = null){
        Log::info(static::class . ": Signature Request Viewed");
    }//end method webhookFolderViewed

    private function webhookFolderSigned($data = null){
        Log::info(static::class . ": Signature Folder Signed");
    }//end method webhookFolderSigned

    private function webhookFolderCanceled($data = null){
        Log::info(static::class . ": Signature Folder Canceled");
    }//end method webhookFolderCanceled

    private function webhookFolderDeleted($data = null){
        Log::info(static::class . ": Signature Folder Deleted");
    }//end method webhookFolderDeleted

    private function webhookFolderExecuted($data = null){
        Log::info(static::class . ": Signature Folder Executed");
    }//end method webhookFolderExecuted

    public function getSignUrl(){}
}//end class ESignGenie
