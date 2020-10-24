<?php
namespace App\Handlers\AdminRequests;

use App\Handlers\Handler;

class OpportunityMakerRequestHandler extends AbstractAdminRequestHandler{
    public function getHandlerType(){
        return "opportunity-maker";
    }//end method getHandlerType

    public function getTextMessage(){
        return " requested to have the priviledge to publish opportunities.";
    }//end method getTextMessage

    public function getTargetId(){
        return "";
    }//end method getTarget
}//end class OpportunityMakerRequestHandler