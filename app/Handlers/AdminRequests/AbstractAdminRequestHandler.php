<?php
namespace App\Handlers\AdminRequests;

use App\Handlers\Handler;

use App\AdminRequest;

abstract class AbstractAdminRequestHandler implements Handler{

    protected $adminRequest;

    public function run(){

    }//end method run

    public function setAdminRequest(AdminRequest $adminRequest){
        $this->adminRequest = $adminRequest;
    }//end method setAdminRequest

    public function getAdminRequest(){
        return $this->adminRequest;
    }//end method getAdminRequest

    public function getUser(){
        return $this->getAdminRequest()->user;
    }//end method getUser

    public function getTextMessage(){
        return " sent the admin a request";
    }//end method getTextMessage

    public function getTargetId(){
        return "";
    }//end method getTarget

    public function getHandlerType(){
        return "default"; //default|report-post
    }//end method getHandlerType

    public function getHandledObject(){
        $adminRequest = $this->getAdminRequest();
        $text = $this->getTextMessage();
        $user = $this->getUser();

        return json_decode(json_encode([
            "id" => $adminRequest->id,
            "type" => $this->getHandlerType(), 
            "viewed" => $adminRequest->viewed,
            "user" => [
                "id" => $user->id,
                "thumbnail" => $user->thumbnail,
                "name" => $user->name,
            ],
            "data" => [
                "text" => $text,
                "target_id" => $this->getTargetId()
            ]
        ]));
    }//end method getHandledObject

    public static function getHandledObjectData(AdminRequest $adminRequest) : object{
        $handlerClass = $adminRequest->handler;

        $handler = new $handlerClass();
        $handler->setAdminRequest($adminRequest);

        return $handler->getHandledObject();
    }//end method getHandledObjectData
}//end class AbstractAdminRequestHandler