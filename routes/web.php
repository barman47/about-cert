<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/cv/pdf/generate/{user}/{template}', "CVController@serveViewForPDFConversion")->name("serveCVViewForPDFConversion");

Route::get('/', function () {
    return view('welcome');
    // return response()->file(base_path() . "/storage/app/documents/Yp9eqJrZ0OHQriRG4IgwLaPBnlTN6sA1pzOaoDJ2.png");
});

Route::get('/pdf/view', function() {
    return view("cvtemplates.CV1.index");
    // return "Reached";
});


Route::get("test", function() {
    // return new App\Mail\VerifyEmailAddress(\App\User::first());
    $user = \App\User::where("email", "davexoyinbo@gmail.com")->first();
    Mail::to($user)->send(new \App\Mail\VerifyEmailAddress($user));
    return new App\Mail\VerifyEmailAddress(\App\User::first());
});

Route::get('/verify-email/{user}', 'AuthenticationController@verifyEmail')->name('verifyEmail');
