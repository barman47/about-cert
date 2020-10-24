<?php

namespace App\Http\Controllers;

use App\Exceptions\OpportunityRepositoryException;
use App\Http\Requests\StoreOpportunityRequest;
use App\Repositories\OpportunityRepository;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    public function index(OpportunityRepository $repo)
    {
        $user = auth()->user();

        $opportunities = $repo->user($user)->fetchAll(null, ["likes"], ["liked"]);

        return response()->json($opportunities, 200);
    } //end method index

    public function store(StoreOpportunityRequest $request, OpportunityRepository $repo)
    {
        $user = auth()->user();

        try {
            $opportunity = $repo
                ->user($user)
                ->content($request->content)
                ->title($request->title)
                ->link($request->link)
                ->create();
        } catch (OpportunityRepositoryException $e) {
            $message = $e->getMessage() ?? "An error occured";
            $code = $e->getCode() == 0 ? 400 : $e->getCode();

            return response()->json(["message" => $message], $code);
        }

        return response()->json($opportunity, 201);
    } //end method store

    public function hasMakerPriviledge(Request $request, OpportunityRepository $repo){
        return response()->json(
            $repo->user(auth()->user())->hasPriviledge()
        );
    }//end method hasMakerPriviledge

}//end class OpportunityController
