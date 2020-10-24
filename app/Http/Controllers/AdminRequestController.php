<?php

namespace App\Http\Controllers;

use App\Exceptions\AdminRequestRepositoryException;
use App\Repositories\AdminRequestRepository;
use Illuminate\Http\Request;

use App\Post;
use App\Opportunity;

class AdminRequestController extends Controller
{
    public function getAdminRequests(Request $request, AdminRequestRepository $adminRequestRepository)
    {
        $user = auth()->user();

        $returnData = $adminRequestRepository->user($user)->getAdminRequests();

        return response()->json(["data" => $returnData]);
    } //end method getAdminRequests

    public function report(Request $request, $type, AdminRequestRepository $adminRequestRepository)
    {
        $request->validate([
            "id" => "required|string",
        ]);

        $typeMapper = [
            "post" => Post::class,
            "opportunity" => Opportunity::class
        ];

        $type = strtolower($type);

        if(!array_key_exists($type, $typeMapper)){
            return response()->json(["message" => "The type '$type' is not supported"], 400);
        }

        $user = auth()->user();
        $model = $typeMapper[$type]::findOrFail($request->id);

        try {
            $adminRequestRepository->user($user)
                ->model($model)
                ->report();
        } catch (AdminRequestRepositoryException $e) {
            $message = $e->getMessage() ?? "An error occured";
            $code = $e->getCode() == 0 ? 400 : $e->getCode();

            return response()->json(["message" => $message], $code);
        }

        return response()->json(["message" => "$type reported"], 201);
    } //end method report

    public function grantOpportunityMakerPriviledgeRequest(Request $request, AdminRequestRepository $adminRequestRepository)
    {

        $user = auth()->user();
        try {
            $adminRequestRepository->user($user)->grantOpportunityMakerPriviledgeRequest();
        } catch (AdminRequestRepositoryException $e) {
            $message = $e->getMessage() ?? "An error occured";
            $code = $e->getCode() == 0 ? 400 : $e->getCode();

            return response()->json(["message" => $message], $code);
        }

        return response()->json(["message" => "Request Made"], 201);
    } //end method grantOpportunityMakerPriviledgeRequest
} //end class AdminRequestController
