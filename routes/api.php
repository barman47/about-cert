<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post("/register", "AuthenticationController@register");
Route::post("/login", "AuthenticationController@login");
Route::post("/test", "DocumentSignatureController@test");


Route::middleware("auth:api")->group(function(){
    Route::get("auth/user", "AuthenticationController@index");
    Route::post("update-user", "AuthenticationController@update");
    Route::post("/logout", "AuthenticationController@logout");

    Route::post("comments", "CommentController@store");

    Route::post("like", "LikeController@store");

    // Profile
    Route::get("profile", "ProfileController@index");

    Route::get("events", "PostController@events");

    Route::post("/{type}/report", "AdminRequestController@report");

    Route::prefix("posts")->group(function(){
        Route::get("{id}", "PostController@show");
        Route::patch("{id}", "PostController@update");
        Route::get("/", "PostController@index");
        Route::post("/", "PostController@store");
        Route::post("share", "PostController@share");
    });

    Route::prefix("countries")->group(function(){
        Route::get("get", "CountryController@index");
    });

    Route::prefix("users")->group(function(){
        Route::get("search", "UserController@search");
        Route::post("follow", "UserController@follow");
        Route::post("unfollow", "UserController@unfollow");
        Route::get("/{id}", "UserController@show");
        Route::get("/{id}/events", "PostController@otherUsersEvents");
        Route::get("/{id}/profile", "ProfileController@otherOtherUserProfile");
    });

    Route::prefix("academic-details")->group(function(){
        Route::post("create", "AcademicDetailsController@store");
    });

    Route::prefix("projects")->group(function(){
        Route::post("create", "ProjectController@store");
    });

    Route::prefix("certificates")->group(function(){
        Route::post("create", "CertificateController@store");
        Route::get("get-document", "CertificateController@getDocument");
    });

    Route::prefix("work-experiences")->group(function(){
        Route::post("create", "WorkExperienceController@store");
    });

    Route::prefix("interests")->group(function(){
        Route::get("get-all", "InterestController@index");
        Route::post("create", "InterestController@store");
    });

    Route::prefix("job-titles")->group(function(){
        Route::get("get-all", "JobTitleController@index");
    });

    Route::prefix("skills")->group(function(){
        Route::post("create", "SkillController@store");
    });

    Route::prefix("languages")->group(function(){
        Route::get("get-all", "LanguageController@index");
        Route::post("create", "LanguageController@store");
    });

    Route::prefix("documents")->group(function(){
        Route::get("root", "DocumentController@index");
        Route::post("create", "DocumentController@store");
        Route::get("folder/{document}", "DocumentController@folder");
        Route::post("forge", "DocumentController@forgeDocument");
        Route::get("get-bread-crumb/{document}", "DocumentController@getBreadCrumb");
        Route::post("share", "DocumentController@shareDocument");
        Route::get("received-sub-folders", "DocumentController@getReceivedSubFolders");
        Route::post("copy-document-to-folder", "DocumentController@copyDocumentToFolder");
        Route::post("move-document-to-folder", "DocumentController@moveDocumentToFolder");
        Route::get("sub-folders", "DocumentController@getSubFolders");
        Route::get("search", "DocumentController@search");
        Route::get("view", "DocumentController@viewDocument");
        Route::get("get-sign-url", "DocumentController@getSignUrl");
        Route::get("get-signed-documents", "DocumentController@getSignedDocuments");
        Route::get("download/{document}", "DocumentController@getDownloadLink");
        
        Route::post("delete", "DocumentController@deleteDocument");
        Route::post("permanently-delete", "DocumentController@permanentlyDeleteDocument");
        Route::post("empty-trash", "DocumentController@emptyTrash");

        Route::post("restore-document", "DocumentController@restoreSingleDocument");
        Route::post("restore-all-documents", "DocumentController@restoreAll");

        Route::prefix("signatures")->group(function(){
            Route::post("create-signature-request", "DocumentSignatureController@createSignatureRequest");
            Route::post("pay-for-priviledge", "DocumentSignatureController@payForPriviledge");
            Route::get("documents/received-markers", "DocumentSignatureController@getSignatureReceiveMarkers");
            Route::get("documents/send-markers", "DocumentSignatureController@getSignatureSendMarkers");
            Route::post("documents/download-signed/to-folder", "DocumentSignatureController@downloadSignedDocumentToFolder");
        });
    });

    Route::prefix("alerts")->group(function(){
        Route::get('/', "AlertController@getAlerts");
        Route::post("mark-as-viewed", "AlertController@markAlertAsViewed");
    });

    Route::prefix("signatures")->group(function(){
        Route::get("get-all", "SignatureController@index");
        Route::post("create", "SignatureController@store");
    });

    Route::prefix("opportunities")->group(function(){
        Route::get("get-all", "OpportunityController@index");
        Route::post("create", "OpportunityController@store");
        Route::get("has-maker-priviledge", "OpportunityController@hasMakerPriviledge");
        Route::post("request-opportunity-maker-priviledge", "AdminRequestController@grantOpportunityMakerPriviledgeRequest");
    });

    Route::prefix("cv")->group(function(){
        Route::get("get-templates", "CVController@getAllCVTemplates");
        Route::get("get-saved-templates", "CVController@getSavedCVTemplates");
        Route::post("pay-for-template", "CVController@payToGrantGroupPriviledge");
        Route::get("temp/generate/download-link", "CVController@onlyDownloadCVLink");
        Route::post("save", "CVController@saveCV");
        Route::post("compile", "CVController@compileCV");
        Route::post("request-for-tailored-cv", "CVController@requestForTailoredCV");
    });

    Route::prefix("admin")->middleware("admin")->group(function(){
        Route::get("is-admin", "AdminController@isAdmin");
        Route::post("is-admin", "AdminController@isAdmin");
        Route::get("user-profile-data", "AdminController@getUserProfileData");
        Route::get("requests", "AdminRequestController@getAdminRequests");
    });

    Route::prefix("live-events")->group(function(){
        Route::get("/", "LiveEventController@getLiveEvents");
        Route::get("/{id}", "LiveEventController@getSingleLiveEvent");
        Route::post("create", "LiveEventController@store");
        Route::post("start-session", "LiveEventController@startSession");
        Route::post("end-session", "LiveEventController@endSession");
        Route::post("send-message", "LiveEventController@sendMessage");
        Route::get("get-messages/{id}", "LiveEventController@getMessages");
        Route::post("send-offer", "LiveEventController@sendOffer");
        Route::post("send-answer", "LiveEventController@sendAnswer");
        Route::post("send-icecandidate", "LiveEventController@sendICECandidate");
    });
});

Route::post("/payment/{gateway}/callback", 'PaymentController@webhook');
Route::post("/signatures/webhook", "DocumentSignatureController@webhook");
Route::get('documents/temp/download/{document}', "DocumentController@downloadGeneratedDocument")->name('tempDocumentDownload');
Route::get("documents/temp/get-file/{document}", "DocumentController@getFile")->name('tempGetDocument');
Route::get('cv/temp/download/', "CVController@onlyDownloadCV")->name('onlyDownloadTempCV');
