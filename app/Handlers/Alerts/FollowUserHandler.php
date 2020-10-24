<?php
namespace App\Handlers\Alerts;
use Log;

use Carbon\Carbon;

class FollowUserHandler extends AlertHandlerAbstractClass{
    public function run(){
    }//end method run

    public function preRun(){
        Log::info($this->getMessage());
    }//end mehtod preRun

    public function getMessage(): string{
        $data = $this->getData();

        return "{$data->name} started following you.";
    }//end method getMessage

    public function getHandledObject() : object {
        $alert = $this->getAlert();
        $sender = $this->getSender();
        $receiver = $this->getReceiver();

        return json_decode(json_encode(
            [
                "id" => $alert->id,
                "type" => "follow",
                "should_view_on_open" => true,
                "created_at" => $alert->created_at,
                "created_at_calendar" => Carbon::create((string) $alert->created_at)->calendar(),
                "viewed" => $alert->viewed,
                "sender" => [
                    "thumbnail" => $sender->thumbnail ?? "/man-avatar-profile-icon.jpg"
                ],
                "data" => [
                    [
                        "text" => $sender->name,
                        "type" => "link", // text|link|button
                        "link_to" => "user", // document|user
                        "id" => $sender->id
                    ],
                    [
                        "text" => " started following you.",
                        "type" => "text", // text|link|button
                    ],
                ]
            ]
        ));
    }//end method getHandledObject
}//end class FollowUserHandler
