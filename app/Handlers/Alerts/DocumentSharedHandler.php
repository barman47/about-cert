<?php
namespace App\Handlers\Alerts;

use App\DocumentType;
use Carbon\Carbon;
use Log;

class DocumentSharedHandler extends AlertHandlerAbstractClass
{
    public function run()
    {

    } //end method run

    public function preRun()
    {
        Log::info($this->getMessage());
        // TODO: send email
    } //end mehtod preRun

    public function getMessage(): string
    {
        $sender = $this->getSender();
        $receiver = $this->getReceiver();
        $document = $this->getData();
        $documentType = $document->type == 'folder' ? 'folder' : 'document';

        $documentName = $document->name . ($document->extension ? ".{$document->extension}" : '');

        return "{$sender->name} sent you a {$documentType} with name {$documentName}.";
    } //end method getMessage

    public function getHandledObject(): object
    {
        $sender = $this->getSender();
        $receiver = $this->getReceiver();
        $document = $this->getData();
        $alert = $this->getAlert();

        $documentType = $document->type == 'folder' ? 'folder' : 'document';

        $documentName = $document->name . ($document->extension ? ".{$document->extension}" : '');

        return json_decode(json_encode(
            [
                "id" => $alert->id,
                "type" => "doucument-received",
                "should_view_on_open" => true,
                "created_at" => $alert->created_at,
                "created_at_calendar" => Carbon::create((string) $alert->created_at)->calendar(),
                "viewed" => $alert->viewed,
                "sender" => [
                    "thumbnail" => $sender->thumbnail ?? "/man-avatar-profile-icon.jpg",
                ],
                "data" => [
                    [
                        "text" => $sender->name,
                        "type" => "link",
                        "link_to" => "user",
                        "id" => $sender->id,
                    ],
                    [
                        "text" => " sent you a {$documentType} with name \"$documentName\". Saved in the folder \"",
                        "type" => "text",
                    ],
                    [
                        "text" => $document->documentable_id == DocumentType::ROOT ? DocumentType::ROOT : $document->documentable()->select("name")->first()->name,
                        "type" => "link",
                        "link_to" => "folder",
                        "id" => $document->documentable_id,
                    ],
                    [
                        "text" => "\"",
                        "type" => "text",
                    ],
                    [
                        "text" => "View File",
                        "type" => "button",
                        "link_to" => "document",
                        "id" => $document->id,
                    ],
                ],
            ]
        ));
    } //end method getHandledObject
} //end class DocumentSharedHandler
