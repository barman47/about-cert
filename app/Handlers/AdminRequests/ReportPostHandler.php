<?php
namespace App\Handlers\AdminRequests;

use App\Handlers\Handler;

class ReportPostHandler extends AbstractAdminRequestHandler{
    public function getHandlerType(){
        return "report-post";
    }//end method getHandlerType

    public function getTextMessage(){
        return " reported a post";
    }//end method getTextMessage

    public function getTargetId(){
        return unserialize($this->getAdminRequest()->meta)["model"]["id"];
    }//end method getTarget
}//end class ReportPostHandler