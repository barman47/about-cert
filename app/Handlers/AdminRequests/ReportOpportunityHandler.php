<?php
namespace App\Handlers\AdminRequests;

use App\Handlers\Handler;

class ReportOpportunityHandler extends AbstractAdminRequestHandler{
    public function getHandlerType(){
        return "report-opportunity";
    }//end method getHandlerType

    public function getTextMessage(){
        return " reported a published opportunity";
    }//end method getTextMessage

    public function getTargetId(){
        return unserialize($this->getAdminRequest()->meta)["model"]["id"];
    }//end method getTarget
}//end class ReportOpportunityHandler