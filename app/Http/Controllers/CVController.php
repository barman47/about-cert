<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\CVTemplate as Template;
use App\CVTemplateGroup as TemplateGroup;
use App\Repositories\CVRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\PriviledgeRepository;
use App\Exceptions\CVAccessDeniedException;
use App\Exceptions\CVRepositoryException;


use App\PaymentGateways\AbstractTemplates\AbstractPaymentTemplate;
use App\PaymentGateways\PaystackGateway;
use App\PaymentGateways\RaveGateway;

use Illuminate\Support\Facades\URL;

use App\User;
use App\Priviledge;
use App\CVTailoredPrice;
use PDF;
use Str;
// PDF::loadView("pdf.withdrawal.admin-report", ["records" => json_decode(json_encode($records))])->save(base_path() . '/resources/pdfs/withdrawal-reports.pdf');

class CVController extends Controller
{
    public function getAllCVTemplates(){
        $user = auth()->user();

        $request = request();

        $request->validate([
            "count" => "integer",
            "page" => "integer"
        ]);

        $count = $request->count ?? 20;
        $page = $request->page ?? 1;

        $cVRepository = new CVRepository();

        $cVRepository->user($user);

        $templateGroupData = $cVRepository->getAllGroupWithPagination($count, $page);

        return response()->json($templateGroupData, 200);
    }//end method getAllCVTemplates

    public function getSavedCVTemplates(){
        $user = auth()->user();

        $request = request();

        $request->validate([
            "count" => "integer",
            "page" => "integer"
        ]);

        $count = $request->count ?? 20;
        $page = $request->page ?? 1;

        $cVRepository = new CVRepository();

        $cVRepository->user($user);

        $templateGroupData = $cVRepository->getAllSavedGroupWithPagination($count, $page);

        return response()->json($templateGroupData, 200);
    }//end method getSavedCVTemplates

    public function payToGrantGroupPriviledge(Request $request, PaymentRepository $paymentRepository, PriviledgeRepository $priviledgeRepository){
        $request->validate([
            "group_id" => "required|string",
            "redirect_url" => "required|string"
        ]);

        $user = auth()->user();

        $templateGroup = TemplateGroup::find($request->group_id);
        $amount = (float) $templateGroup->price;
        $redirectUrl = $request->redirect_url;

        $paymentRepository->user($user);

        $meta = [
            [
                "metaname" => "payment_for",
                "metavalue" => "template_group"
            ],
            [
                "metaname" => "target",
                "metavalue" => $request->group_id
            ],
            [
                "metaname" => "user_id",
                "metavalue" => $user->id
            ]
        ];

        if(!$paymentRepository->makePayment($amount, $redirectUrl, $meta))
            return response()->json("An error occured", 400);

        return response()->json(["payment_link" => $paymentRepository->getPaymentLink()]);
    }//end method payToGrantPriviledge

    public function onlyDownloadCVLink(){
        $request = request();

        $request->validate([
            "template_id" => "required|string",
            "intent" => "required|string", // download_only|download_saved
        ]);

        $user = auth()->user();

        $template = Template::find($request->template_id);

        $cVRepository = new CVRepository();
        $cVRepository->user($user)->template($template);

        try{
            if($request->intent == "download_saved")
                $downloadLink = $cVRepository->generateTemporaryDownloadLinkForSavedTemplate();
            else
                $downloadLink = $cVRepository->generateTemporaryDownloadLinkForTemplate();
        }catch(CVAccessDeniedException $e){
            return response()->json(["message" => "Access denied"], 400);
        }catch(CVRepositoryException $e){
            return response()->json(["message" => $e->getMessage()], $e->getCode() == 0 ? 400 : $e->getCode());
        }

        return response()->json(["download_url" => $downloadLink]);
    }//end method onlyDownloadCVLink

    public function onlyDownloadCV(){
        $request = request();

        if (! $request->hasValidSignature()) {
            abort(401);
        }

        $extension = pathinfo($request->path)['extension'];

        $name = $request->has("name") ? $request->name : "about-cert-cv";

        $name = $name . ($extension ? ".$extension" : '');

        return response()->download($request->path, $name);
    }//end method onlyDownloadCV

    public function saveCV(Request $request){
        $request->validate([
            "template_id" => "required|string"
        ]);

        $user = auth()->user();
        $template = Template::findOrFail($request->template_id);

        $cVRepository = new CVRepository();
        $cVRepository->user($user)->template($template);

        try{
            if(!$cVRepository->create()){
                return response()->json(["message" =>"An error occured"], 400);
            }
        }catch(CVAccessDeniedException $e){
            return response()->json(["message" => "User does not have access to the template"], 400);
        }

        return response()->json(["message" => "CV saved"], 201);
    }//end method saveCV

    public function compileCV(Request $request){
        $request->validate([
            "template_id" => "required|string"
        ]);

        $user = auth()->user();
        $template = Template::findOrFail($request->template_id);

        $cVRepository = new CVRepository();
        $cVRepository->user($user)->template($template);

        try{
            if(!$cVRepository->createFile()){
                return response()->json(["message" =>"An error occured"], 400);
            }
        }catch(CVAccessDeniedException $e){
            return response()->json(["message" => "User does not have access to the template"], 400);
        }

        return response()->json(["message" => "CV generated"], 201);
    }//end method compileCV

    public function requestForTailoredCV(Request $request, PaymentRepository $paymentRepository, PriviledgeRepository $priviledgeRepository){
        $request->validate([
            "redirect_url" => "required|string",
            "type" => "required|string",
            "job_title" => "required|string",
            "others" => "string"
        ]);

        $user = auth()->user();
        $reflectionClass = new \ReflectionClass(CVTailoredPrice::class);
        $tailoredType = strtoupper($request->type);

        if(!in_array($tailoredType, array_keys($reflectionClass->getConstants())))
            return response()->json(["message" => "Invalid Template Type"], 400);

        // $templateGroup = TemplateGroup::where("group_code", "tailored")->first();
        $amount = (float) $reflectionClass->getConstant($tailoredType);
        $redirectUrl = $request->redirect_url;

        $paymentRepository->user($user);

        $meta = [
            [
                "metaname" => "payment_for",
                "metavalue" => "tailored_template"
            ],
            [
                "metaname" => "type",
                "metavalue" => $tailoredType
            ],
            [
                "metaname" => "job_title",
                "metavalue" => $request->job_title
            ],
            [
                "metaname" => "others",
                "metavalue" => $request->others ?? ''
            ],
            [
                "metaname" => "user_id",
                "metavalue" => $user->id
            ]
        ];

        if(!$paymentRepository->makePayment($amount, $redirectUrl, $meta))
            return response()->json(["message" => "An error occured"], 400);

        return response()->json(["payment_link" => $paymentRepository->getPaymentLink()]);
    }//end method requestForTailoredCV

    public function serveViewForPDFConversion(Request $request, User $user, Template $template){
        // TODO: Uncomment the next block of code in and including the if statement
        // if (! $request->hasValidSignature()) {
        //     abort(401);
        // }

        $records = array();

        // Evaluate user name
        $names = explode(" ", preg_replace('/\s+/i', " ", $user->name));
        $lastName = $names[0];
        $otherNames = trim(ltrim($user->name, $lastName));
        $user->lastName = $lastName;
        $user->otherNames = $otherNames;
        $user->entity;

        $records["user"] = $user;
        $records["skills"] = $user->skills;
        $records["certificates"] = $user->certificates;
        $records["languages"] = $user->languages;
        $records["workExperiences"] = $user->workExperiences;
        $records["projects"] = $user->projects;
        $records["interests"] = $user->interests;

        return view(CVRepository::CV_VIEW_BASE_FOLDER . ".{$template->template_file}", ["records" => (object) $records]);
        // return view(CVRepository::CV_VIEW_BASE_FOLDER . ".CV3.index", ["records" => (object) $records]);
    }//end method serveViewForPDFConversion
}//end class CVController
