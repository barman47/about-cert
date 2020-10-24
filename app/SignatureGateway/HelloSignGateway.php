<?php
namespace App\SignatureGateway;

use App\EditedVendor\HelloSign\Client;
use HelloSign\SignatureRequest;
use HelloSign\EmbeddedSignatureRequest;


class HelloSignGateway extends SignatureGatewayAbstractClass{
    public function createSignatureRequest(){
        $client = new Client(config("app_signature.hellosign.api_key"));
        $request = new SignatureRequest;
        $request->enableTestMode();
        $request->setSubject('My First embedded signature request with a template');
        $request->setMessage('Awesome, right?');
        $request->addSigner('jack@example.com', 'Jack');
        $request->addFile(storage_path() . '/app/file.docx');
        // $request->addFile($path_to_file_1_pdf);

        $client_id = config("app_signature.hellosign.client_id");
        $embedded_request = new EmbeddedSignatureRequest($request, $client_id);
        $response = $client->createEmbeddedSignatureRequest($embedded_request);

        return $response->toArray();
    }//end method sign

    public function getSignUrl(){
        $client = new Client(config("app_signature.hellosign.api_key"));
        $response = $client->getEmbeddedSignUrl('dcf71d97b15e1ecfa5803836de2fcd9a');

        return $response->toArray();
    }//end method sign


    public function getFiles(){
        $client = new Client(config("app_signature.hellosign.api_key"));

        //signature_request_id
        return $client->getFiles('190393f76c111554af1ba3af42213088b5e845ae', public_path(), SignatureRequest::FILE_TYPE_ZIP);
    }//end method getFiles
}//end class HelloSignGateway
