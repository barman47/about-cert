<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\DocumentSignatureRepository;
use App\Exceptions\DocumentSignatureRepositoryException;
use App\SignatureGateway\SignatureGatewayAbstractClass;

use App\Repositories\PriviledgeRepository;
use App\Repositories\PaymentRepository;

use App\User;
use App\Document;
use App\SignaturePriviledge;

use App\SignatureSendMarker;

class DocumentSignatureController extends Controller
{
    private $repository;

    public function __construct(DocumentSignatureRepository $repository){
        $this->repository = $repository;
    }//end constructor

    public function test(){
        // $client = new \GuzzleHttp\Client();

        // $response = $client->get("https://www.esigngenie.com/esign/api/folders/document/download?folderId=3360067&docNumber=1"  , [
        //     "headers" => [
        //         "Accept" => "application/pdf",
        //         "Authorization" => "Bearer e01464b9ab554a7d9714f79028258926"
        //     ]
        // ]);

        // $resource = $response->getBody();

        // $tempName = \Str::random(30) . ".pdf";

        // \Storage::put("/temp/{$tempName}" , $resource);
        // \Storage::copy("/temp/{$tempName}", "/temp/downloaded3.pdf");
        // \Storage::delete("/temp/{$tempName}");

        $file = new \Illuminate\Http\File(storage_path("/app/temp/downloaded3.pdf"));

        \Storage::putFileAs("temp", $file, "downloaded4.pdf");
        \Storage::putFileAs("temp", $file, "downloaded5.pdf");
        return "OK";
    }//end method test

    public function createSignatureRequest(Request $request){
        $request->validate([
            "document_id" => "required|string",
            "receiver_ids" => "required|string",
            "outsiders" => "string"
        ]);

        $user = auth()->user();
        $document = Document::findOrFail($request->document_id);
        $receivers = User::whereIn("id", json_decode($request->receiver_ids, true))->get();

        $outsiders = $request->has("outsiders") ?  json_decode($request->outsiders) : [];

        foreach($outsiders as $outsider){
            if(!$outsider->name && !$outsider->email)
                continue;
            if($receivers->where("email", $outsider->email)->first() != null)
                continue;

            $tempUser = User::where("email", $outsider->email)->first();
            if($tempUser == null){
                $receivers->add($outsider);
            }else{
                $receivers->add($tempUser);
            }
        }

        if(count($receivers) == 0){
            return response()->json(["message" => "The receivers object is empty."], 401);
        }

        $this->repository->user($user)
                ->document($document)
                ->receivers($receivers);

        try{
            $response = $this->repository->createSignatureRequest();
        }catch(DocumentSignatureRepositoryException $e){
            if($e->getCode() != 0){
                return response()->json(["message" => $e->getMessage()], $e->getCode());
            }

            return response()->json("An error occured", 500);
        }

        return response()->json($response, 201);
    }//end method createSignatureRequest

    public function getSignatureReceiveMarkers(Request $request){
        $user = auth()->user();

        $markers = $this->repository
                        ->user($user)
                        ->getSignatureReceiveMarkers();

        return response()->json(["data" => $markers]);
    }//end method getSignatureReceiveMarkers

    public function getSignatureSendMarkers(Request $request){
        $user = auth()->user();

        $markers = $this->repository
                        ->user($user)
                        ->getSignatureSendMarkers();

        return response()->json(["data" => $markers]);
    }//end method getSignatureReceiveMarkers

    public function webhook(Request $request){
        $this->repository->webhook($request);
    }//end method webhook

    public function payForPriviledge(Request $request){
        $request->validate([
            "plan" => "required|string",
            "redirect_url" => "required|string"
        ]);

        try{
            $paymentLink = $this->repository->payForPriviledge($request->plan, $request->redirect_url);
        }catch(DocumentSignatureRepositoryException $e){
            // throw $e;
            return response()->json(["message" => $e->getMessage()], $e->getCode() == 0 ? 400 : $e->getCode());
        }//end try-catch
        

        return response()->json(["payment_link" => $paymentLink]);
    }//end method payToGrantPriviledge

    public function downloadSignedDocumentToFolder(Request $request){
        $request->validate([
            "signature_send_marker_id" => "string|required",
            "folder_id" => "string",
        ]);

        $user = auth()->user();
        try{
            $document = $this->repository->user($user)
                            ->downloadSignedDocumentToFolder(SignatureSendMarker::find($request->signature_send_marker_id), $request->folder_id);
        }catch(DocumentSignatureRepositoryException $e){
            return response()->json(["message" => $e->getMessage()], 403);
        }

        return response()->json(["document" => $document], 201);
    }//end method downloadSignedDocumentToFolder
}//end class DocumentSignatureController
