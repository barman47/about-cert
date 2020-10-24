<?php
namespace App\Handlers\Alerts;

use App\Handlers\Handler;

use App\Alert;
use Carbon\Carbon;

abstract class AlertHandlerAbstractClass implements Handler{
    protected $alert;

    public abstract function preRun(); //to send emails
    public abstract function getMessage(): string;

    protected function getAlert(){
        return $this->alert;
    }//end method getAlert

    public function setAlert(string $id){
        $this->alert = Alert::findOrFail($id);
    }//end method getAlert

    protected function getData(){
        return \unserialize($this->getAlert()->data);
    }//end method getData

    public function getSender(){
        $senderClass = $this->alert->sender_type;

        return $senderClass::findOrFail($this->alert->sender_id);
    }//end method getSender

    public function getReceiver(){
        $receiverClass = $this->alert->receiver_type;

        return $receiverClass::findOrFail($this->alert->receiver_id);
    }//end method getReceiver

    public function getHandledObject() : object {
        $alert = $this->getAlert();
        $sender = $this->getSender();
        $receiver = $this->getReceiver();

        return json_decode(json_encode(
            [
                "id" => $alert->id,
                "type" => "alert-default",
                "should_view_on_open" => true,
                "created_at" => $alert->created_at,
                "created_at_calendar" => Carbon::create((string) $alert->created_at)->calendar(),
                "viewed" => $alert->viewed,
                "sender" => [
                    "thumbnail" => $sender->thumbnail ?? "/man-avatar-profile-icon.jpg"
                ],
                "data" => [
                    [
                        "text" => $this->getMessage(),
                        "type" => "text", // text|link|button
                        // "link_to" => "document", // document|user
                        // "id" => "an identifier"
                    ],
                ]
            ]
        ));
    }//end method getHandledObject

    public static function getHandledObjectData(Alert $alert) : object{
        $handlerClass = $alert->handler;

        $handler = new $handlerClass();
        $handler->setAlert($alert->id);

        return $handler->getHandledObject();
    }//end method getHandledObjectData
}// end abstract class AlertHandlerAbstractClass
