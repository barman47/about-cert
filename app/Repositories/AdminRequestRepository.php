<?php
namespace App\Repositories;

use App\AdminRequest;
use App\Exceptions\AdminRequestRepositoryException;
use App\Handlers\AdminRequests\AbstractAdminRequestHandler;
use App\Handlers\AdminRequests\OpportunityMakerRequestHandler;
use App\Handlers\AdminRequests\ReportOpportunityHandler;
use App\Handlers\AdminRequests\ReportPostHandler;
use App\Opportunity;
use App\Post;
use App\User;
use Illuminate\Database\Eloquent\Model;

use App\Repositories\PriviledgeRepository;
use App\Priviledge;

class AdminRequestRepository
{
    const OPPORTUNITY_MAKER_TARGET_FIELD_TRAILER = "opportunity-maker.";
    const REPORT_MODEL_SUFFIX = ".REPORT.";
    const OPPORTUNITY_MAkER_SUFFIX = ".OPPORTUNITY-MAKER.";

    const MODEL_HANDLER_MAPPER = [
        Post::class . AdminRequestRepository::REPORT_MODEL_SUFFIX => ReportPostHandler::class,
        Opportunity::class . AdminRequestRepository::REPORT_MODEL_SUFFIX => ReportOpportunityHandler::class,
        Opportunity::class . AdminRequestRepository::OPPORTUNITY_MAkER_SUFFIX => OpportunityMakerRequestHandler::class,
    ];

    private $user;
    private $model;

    public function user(User $user): AdminRequestRepository
    {
        $this->user = $user;
        return $this;
    } //end method user

    function model(Model $model): AdminRequestRepository
    {
        $this->model = $model;
        return $this;
    } //end method model

    function report(): bool
    {
        if (!$this->user) {
            throw new AdminRequestRepositoryException("User must be specified");
        }

        if (!$this->model) {
            throw new AdminRequestRepositoryException("Model must be specified");
        }

        $prefix = get_class($this->model) . static::REPORT_MODEL_SUFFIX;

        if (!array_key_exists($prefix, static::MODEL_HANDLER_MAPPER)) {
            throw new AdminRequestRepositoryException("Report for this model is not supported");
        }

        $user = $this->user;
        $model = $this->model;


        $uniqueField = $prefix . "{$model->id}";

        $adminRequest = AdminRequest::where([
            ["user_id", $user->id],
            ["target_field", $uniqueField],
        ])->first();

        if ($adminRequest !== null) {
            return true;
        }

        $adminRequest = new AdminRequest([
            "user_id" => $user->id,
            "target_field" => $uniqueField,
            "meta" => serialize([
                "model" => [
                    "id" => $model->id
                ]
            ]),
            "handler" => static::MODEL_HANDLER_MAPPER[$prefix]
        ]);

        $adminRequest->save();

        return true;
    } //end method repoortPost

    function grantOpportunityMakerPriviledgeRequest()
    {
        if (!$this->user) {
            throw new AdminRequestRepositoryException("User must be specified");
        }

        $user = $this->user;
        $prefix = Opportunity::class . static::OPPORTUNITY_MAkER_SUFFIX;
        $uniqueField = $prefix . "{$user->id}";

        $adminRequest = AdminRequest::where([
            ["user_id", $user->id],
            ["target_field", $uniqueField],
        ])->first();

        if ($adminRequest !== null) {
            return true;
        }

        $adminRequest = new AdminRequest([
            "user_id" => $user->id,
            "target_field" => $uniqueField,
            "handler" => static::MODEL_HANDLER_MAPPER[$prefix]
        ]);

        $adminRequest->save();

        return true;
    } //end method grantOpportunityMakerPriviledgeRequest

    function grantOpportunityMakerPriviledge(User $user){
        if (!$this->user) {
            throw new AdminRequestRepositoryException("User must be specified");
        }

        if(!$this->hasAdminPriviledge()){
            throw new AdminRequestRepositoryException("Current user does not have admin priviledge");
        }

        $priviledgeRepository = new PriviledgeRepository();
        $priviledge = Priviledge::where([
            ["code", "opportunity_maker"]
        ])->first();
        
        $priviledgeRepository->user($user)->priviledge($priviledge)->create();
    }//end method grantOpportunityMakerPriviledge

    function getAdminRequests()
    {
        if (!$this->user) {
            throw new AdminRequestRepositoryException("User must be specified");
        }

        if (!$this->hasAdminPriviledge()) {
            throw new AdminRequestRepositoryException("User does not have admin priviledge");
        }

        $returnData = [];

        $adminRequests = AdminRequest::with("user")->latest()->paginate();

        $adminRequests->each(function ($adminRequest) use (&$returnData) {
            $returnData[] = $this->getHandledObjectData($adminRequest);
        });

        $adminRequests = json_decode($adminRequests->toJson());

        $adminRequests->data = $returnData;

        return $adminRequests;
    } //end method getAdminRequests

    function hasAdminPriviledge(): bool
    {
        return (new AdminUserRepository())->user($this->user)->hasAdminPriviledge();
    } //end method hasAdminPriviledge

    function getHandledObjectData($adminRequest)
    {
        return AbstractAdminRequestHandler::getHandledObjectData($adminRequest);
    } //end method getHandledObjectData
} //end class AdminRequestRepository
