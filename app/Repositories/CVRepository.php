<?php
namespace App\Repositories;

use Str;
use PDF2;
use PDF;
use URL;


use App\Jobs\DeleteTemporaryCV;
use App\Exceptions\CVRepositoryException;
use App\Exceptions\CVAccessDeniedException;

use App\CVTemplate;
use App\CVTemplateGroup;
use App\User;
use App\UserCV;
use App\Priviledge;
use App\UserPriviledge;

use App\Repositories\PaymentRepository;

use App\CVGenerators\CVGenerator;

use App\Handlers\Alerts\TailoredCVHandler;
// use Nesk\Puphpeteer\Puppeteer;
// use Its404\PhpPuppeteer\Browser;
use Spatie\Browsershot\Browsershot;
use VerumConsilium\Browsershot\Facades\PDF as PDF3;


class CVRepository{
    const CV_VIEW_BASE_FOLDER = "cvtemplates";

    private $template;
    private $user;
    private $pivotData;

    private $priviledgeRepository;

    public function __construct(){
        $this->priviledgeRepository = new PriviledgeRepository();
    }//end constructor

    public function getAllGroupWithPagination($count = 20, $page = 1)
    {
        $templateGroupPaginationData = (new CVTemplateGroup())->customPaginate($count, $page);

        if($templateGroupPaginationData == null)
            throw new CVRepositoryException("An Error just occurred");

        if($this->user != null){
            $user = $this->user;
            foreach($templateGroupPaginationData->data as $datum){
                if($datum->group_code == "free"){
                    $datum->has_access = 1;
                    foreach($datum->templates as $template){
                        $template->has_access = 1;
                    }
                }
                elseif(
                    $datum->group_code == "on_order" ||
                    $datum->group_code == "tailored" ||
                    $datum->group_code == "on_order"
                ){
                    $datum->has_access = null;
                    foreach($datum->templates as $template){
                        $template->has_access = $this->hasAccessToTemplate($template) ? 1 : 0;
                    }
                }else{
                    $datum->has_access = $this->hasAccessToTemplateGroup($datum) ? 1 : 0;
                    foreach($datum->templates as $template){
                        $template->has_access = null;
                    }
                }
            }
        }//end if

        return $templateGroupPaginationData;
    }//end method getAllWithPagination

    public function getAllSavedGroupWithPagination($count = 20, $page = 1)
    {
        $user = $this->user;

        $cvTemplates = $user->cVs;
        $cvTemplateIds = $cvTemplates->pluck("c_v_template_group_id")->toArray();

        $templateGroupPaginationData = (new CVTemplateGroup())->customPaginateWhereIn('id', $cvTemplateIds)->customPaginate($count, $page);

        if($templateGroupPaginationData == null)
            throw new CVRepositoryException("An Error just occurred");

        $cvTemplateIds = $cvTemplates->pluck("id")->toArray();

        foreach($templateGroupPaginationData->data as $ukey => $datum){
            $tempTemplates = [];
            foreach($datum->templates as $key => $template){
                if(in_array($template->id, $cvTemplateIds)){
                    $tempTemplates[] = $template;
                }
            }
            unset($datum->templates);
            $datum->templates = $tempTemplates;

            if($datum->group_code == "free"){
                $datum->has_access = 1;
                foreach($datum->templates as $template){
                    $template->has_access = 1;
                }
            }
            elseif(
                $datum->group_code == "on_order" ||
                $datum->group_code == "tailored" ||
                $datum->group_code == "on_order"
            ){
                $datum->has_access = null;
                foreach($datum->templates as $template){
                    $template->has_access = $this->hasAccessToTemplate($template) ? 1 : 0;
                }
            }else{
                $datum->has_access = $this->hasAccessToTemplateGroup($datum) ? 1 : 0;
                foreach($datum->templates as $template){
                    $template->has_access = null;
                }
            }

            if(count($datum->templates) < 1){
                $templateGroupPaginationData->data->forget($ukey);
            }
        }

        return $templateGroupPaginationData;
    }//end method getAllSavedGroupWithPagination

    public function template(CVTemplate $template) : CVRepository
    {
        $this->template = $template;
        return $this;
    }//end static method template

    public function user(User $user): CVRepository
    {
        $this->user = $user;
        return $this;
    }//end method user

    public function pivotData($array = []){
        $this->pivotData = $array;
        return $this;
    }//end method pivotData

    public function recompile(): CVRepository
    {
        if($this->template == null || $this->user == null)
            throw new CVRepositoryException("Template or User object not defined");

        if($this->user->cVs()->where("c_v_templates.id", $this->template->id)->first() == null){
            throw new CVRepositoryException("There is no relationship between the user and the cv template");
        }

        // TODO: Recompile the template for this user
        // TODO: update the data

        $userCV  = $this->user->cVs()->where("c_v_templates.id", $this->template->id)->first()->pivot;

        $userCV->src = $this->generateCVPDF($userCV->src ?? null);

        $userCV->save();

        return $this;
    }//end method update

    public function update(): CVRepository
    {
        if($this->template == null || $this->user == null)
            throw new CVRepositoryException("Template or User object not defined");

        if(count($this->array) == 0)
            return $this;

        if($user->cVs()->where("id", $this->template->id)->first() != null){
            if($this->hasAccessToTemplate())
                $user->cVs()->updateExistingPivot($this->template->id, $this->$array);
            else
                throw new CVRepositoryException("This user does not have access to this template");
        }else{
            throw new CVRepositoryException("There is no relationship between the user and the cv template");
        }

        return $this;
    }//end method update

    public function create() : bool{
        if($this->template == null || $this->user == null)
            throw new CVRepositoryException("Template or User object not defined");

        if($this->hasAccessToTemplate()){
            if($this->user->cVs()->where("c_v_templates.id", $this->template->id)->first() == null){
                $folder = "app/cvs/{$this->user->username}/";
                $fullPath = $folder . Str::random(30) . ".pdf";

                $this->user->cVs()->attach($this->template->id, [
                    "src" => ""
                ]);
                return true;
            }else{
                return true;
            }
        }
        else{
            throw new CVAccessDeniedException("This user does not have access to this template or template group");
        }

        return false;
    }//end method create

    public function createFile() : CVRepository
    {
        if($this->template == null || $this->user == null)
            throw new CVRepositoryException("Template or User object not defined");

        if($this->hasAccessToTemplate()){
            if($this->user->cVs()->where("c_v_templates.id", $this->template->id)->first() == null){
                $this->user->cVs()->attach($this->template->id, [
                    "src" => $this->generateCVPDF()
                ]);
            }else{
                $this->recompile();
            }
        }
        else{
            throw new CVAccessDeniedException("This user does not have access to this template or template group");
        }

        return $this;
    }//end static method create

    public function destroy() : CVRepository
    {
        if($this->template == null || $this->user == null)
            throw new CVRepositoryException("Template or User object not defined");

        if($this->user->cVs()->where("id", $this->template->id)->first() != null){
            $this->user->cVs()->detach($this->template->id);
        }else{
            throw new CVRepositoryException("There is no connection between the user and template");
        }

        return $this;
    }//end method destroy

    public function generateTemporaryDownloadLinkForTemplate(): string{
        if($this->template == null || $this->user == null)
            throw new CVRepositoryException("Template or User object not defined");


        if(!$this->hasAccessToTemplate()){
            throw new CVAccessDeniedException("Access denied");
        }

        $path = storage_path($this->generateCVPDF());


        DeleteTemporaryCV::dispatch($path)->delay(now()->addMinutes(6));

        return URL::temporarySignedRoute(
                    'onlyDownloadTempCV', now()->addMinutes(5), [
                            'path' => $path,
                            'name' => "{$this->user->username}-{$this->template->name}-AboutCERT"
                        ]
                );
    }//end method generateTemporaryDownloadLinkForTemplate

    public function generateTemporaryDownloadLinkForSavedTemplate(): string{
        if($this->template == null || $this->user == null)
            throw new CVRepositoryException("Template or User object not defined");


        if(!$this->hasAccessToTemplate()){
            throw new CVAccessDeniedException("Access denied");
        }

        $userCV = UserCV::where([
            ["user_id", $this->user->id],
            ["c_v_template_id", $this->template->id]
        ])->first();

        if($userCV == null || !$userCV->src){
            throw new CVRepositoryException("Compile CV template first", 404);
        }

        $path = storage_path($userCV->src);

        return URL::temporarySignedRoute(
                    'onlyDownloadTempCV', now()->addMinutes(5), [
                        'path' => $path,
                        'name' => "{$this->user->username}-{$this->template->name}-AboutCERT"
                    ]
                );
    }//end method generateTemporaryDownloadLinkForSavedTemplate

    public function requestTailoredCV($data): CVRepository{
        $alertRepository = new AlertRepository();
        $alertRepository
            ->sender($this->user)
            ->receiver($this->user)
            ->data($data)
            ->forAdmin()
            ->handler(new TailoredCVHandler())
            ->create();
        return $this;
    }//end method requestTailoredCV

    public function paymentWebhook($meta){
        $templateGroup = null;
        $user = null;

        foreach($meta as $m2){
            if($m2["metaname"] == "target" && $m2["metavalue"])
                $templateGroup = CVTemplateGroup::find($m2["metavalue"]);
            if($m2["metaname"] == "user_id" && $m2["metavalue"])
                $user = User::find($m2["metavalue"]);
        }

        $priviledge = Priviledge::where("code", "template_group")->first();
        $priviledgeRepository = new PriviledgeRepository;
        $priviledgeRepository->target($templateGroup->id)->user($user)->priviledge($priviledge)->create();

        RevokePriviledge::dispatch($user, $priviledge, $templateGroup->id)->delay(now()->addWeeks(4));
        Log::info("Priviledge granted for payment");
    }//end method paymentWenhook

    public function paymentWebhookForTailoredCV($meta){
        $user = null;
        $data = [];

        foreach($meta as $m2){
            if($m2["metaname"] == "user_id" && $m2["metavalue"])
                $user = User::findOrFail($m2["metavalue"]);
            if(in_array($m2["metaname"], ["job_title", "others", "type"]) && $m2["metavalue"])
                $data[$m2["metaname"]] = $m2["metavalue"];
        }

        $this->user($user)->requestTailoredCV($data);
        Log::info("Payment for the tailored cv logged");
    }//end method paymentWebhookForTailoredCV

    private function hasAccessToTemplate(CVTemplate $template = null): bool
    {
        $template = $template ?? $this->template;

        if($template == null || $this->user == null)
            throw new CVRepositoryException("Template or User object not defined");

        $target = null;
        $priviledge = null;

        if($template->group->group_code == "free"){
            // $target = $template->group;
            // $priviledge = Priviledge::where("code", "free_template_group")->first();
            return true;
        }
        elseif(
            $template->group->group_code == "tailored" ||
            $template->group->group_code == "on_order"
        ){
            $target = $template;
            $priviledge = Priviledge::where("code", "template")->first();
        }else{
            $target = $template->group;
            $priviledge = Priviledge::where("code", "template_group")->first();
        }

        unset($template->group);

        return $this->priviledgeRepository
                    ->user($this->user)
                    ->priviledge($priviledge)
                    ->target($target->id)
                    ->hasPriviledge();
    }//end method hasAccessToTemplate

    private function hasAccessToTemplateGroup(CVTemplateGroup $templateGroup): bool
    {
        $target = $templateGroup;
        $priviledge = null;

        if($target->group_code == "free"){
            // $priviledge = Priviledge::where("code", "free_template_group")->first();
            return true;
        }else{
            $priviledge = Priviledge::where("code", "template_group")->first();
        }

        return $this->priviledgeRepository
                    ->user($this->user)
                    ->priviledge($priviledge)
                    ->target($target->id)
                    ->hasPriviledge();
    }// end mathod hasAccessToTemplateGroup

    private function generateCVPDF($inputPath = null): string {
        if($this->template == null || $this->user == null)
            throw new CVRepositoryException("Template or User object not defined");

        $cvGenerator = new CVGenerator();
        return $cvGenerator->user($this->user)
                    ->template($this->template)
                    ->run($inputPath);
    }//end method generateCVPDF
}//end class CVRepository
