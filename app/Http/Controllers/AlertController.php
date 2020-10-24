<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\AlertRepository;

use App\Exceptions\AlertRepositoryException;

use App\Alert;

class AlertController extends Controller
{
    public function getAlerts(Request $request, AlertRepository $alertRepository){

        $user = auth()->user();

        try{
            $alerts = $alertRepository->user($user)->getAlerts();
        }catch(AlertRepositoryException $e){
            return response()->json(["message" => $e->getMessage() ?? "An error occured"], 400);
        }

        return response()->json(["data" => $alerts], 200);
    }//end method getAlerts

    public function markAlertAsViewed(Request $request, AlertRepository $alertRepository){
        $request->validate([
            "alert_id" => "required|string"
        ]);

        $user = auth()->user();

        try{
            $alert = Alert::findorFail($request->alert_id);
        }catch(\Exception $e){
            return response()->json(["message" => "Invalid alert id"], 400);
        }

        try{
            if(!$alertRepository->user($user)->markAlertAsViewed($alert)){
                return response()->json(["message" => "The user is not the owner of the alert"], 403);
            }
        }catch(AlertRepositoryException $e){
            return response()->json(["message" => $e->getMessage()], 400);
        }

        return response()->json(["message" => "Alert marked as viewed"], 201);
    }//end method markAlertAsViewed
}//end class AlertController
