<?php

namespace App\Http\Controllers;

use App\Repositories\DocumentRepository;
use App\Company;
use App\CVTemplateGroup;
use App\Jobs\DeleteUnverifiedUsersJob;
use App\Jobs\ResizeCoverPhoto;
use App\Jobs\ResizeProfilePhoto;
use App\Mail\VerifyEmailAddress;
use App\Mail\Welcome;
use App\Person;
use App\Priviledge;
use App\Repositories\PriviledgeRepository;
use App\School;
use App\User;
use Auth;
use Carbon\Carbon;
use Hash;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Mail;
use Storage;

class AuthenticationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        $user->loadMissing("entity");

        if ($user->entity_type == Person::class) {
            $user->type = "person";
        } elseif (($user->entity_type == Company::class)) {
            $user->type = "company";
        } elseif (($user->entity_type == School::class)) {
            $user->type = "school";
        }

        $user->makeHidden([
            "entity_id",
            "entity_type",
        ]);

        $user->entity->makeHidden([
            "id",
            "created_at",
            "updated_at",
        ]);

        // $entityValues = $user->entity->toArray();

        $user = array_merge($user->toArray(), $user->entity->toArray());

        unset($user["entity"]);

        return response()->json(["user" => $user]);
    } //end method index

    public function register(Request $request, DocumentRepository $documentRepository)
    {
        $request->validate([
            "name" => "required|string",
            "mobile_number" => "required",
            "email" => "required|email",
            "password" => "required|string|confirmed",
            "type" => "required|string",
            "gender" => "required|string",
        ]);

        $name = $request->name;
        $password = $request->password;
        $email = $request->email;

        $errors = [];

        //Check if user with the email exists
        if (User::where('email', $email)->first() != null) {
            $errors["email"][] = "Email already exists";
        }

        //Validate regex
        //name validation
        $name = preg_replace("/\s+/", " ", trim($name));
        $nameRegexPattern = "/^([a-zA-Z\x{00C0}-\x{00FF}]+( [a-zA-Z\x{00C0}-\x{00FF}]+)+)$/";
        if (!preg_match($nameRegexPattern, $name)) {
            $errors["name"][] = "Invalid name input";
            $errors["name"][] = "At least two names must be given";
            $errors["name"][] = "Letters and spaces only";
        }

        //password validation
        if (!preg_match("/[a-z]/", $password)) {
            $errors["password"][] = "Must contain lowercase";
        }
        if (!preg_match("/[A-Z]/", $password)) {
            $errors["password"][] = "Must contain uppercase";
        }
        if (!preg_match("/[0-9]/", $password)) {
            $errors["password"][] = "Must contain numeric values";
        }

        if (count($errors) > 0) {
            return response()->json([
                "message" => "Invalid input values",
                "errors" => $errors,
            ], 406);
        }

        $entity;

        if (!\in_array($request->type, ["person", "company", "school"])) {
            return response()->json(["message" => "Invalid type " . $request->type . " must be 'person', 'company' or 'school'"], 400);
        }

        if ($request->type == "person") {
            $entity = new Person;
            $entity->gender = $request->gender;
            $entity->phone_number = $request->mobile_number;
        } elseif ($request->type == "school") {
            $entity = new School;
        } elseif ($request->type == "company") {
            $entity = new Company;
        }

        $entity->save();

        try {
            $user = new User([
                "username" => $this->suggestUsername($email, $name),
                "email" => $email,
                "allowed_devices" => json_encode([]),
                "password" => Hash::make($password),
                "name" => $name
            ]);
            $entity->root()->save($user);
        } catch (QueryException $e) {
            $entity->delete();
            throw $e;
        }

        $priviledge = Priviledge::where("code", "free_template_group")->first();
        $priviledgeRepository = new PriviledgeRepository();

        $priviledgeRepository
            ->priviledge($priviledge)
            ->user($user)
            ->target(CVTemplateGroup::where("group_code", "free")->first()->id)
            ->create();

        $documentRepository->user($user)->validateReceivedFolderExistence();
        // $user->documents()->create([
        //         "name" => "Signed Documents",
        //         "documentable_type" => DocumentType::ROOT,
        //         "documentable_id"   => DocumentType::ROOT,
        //         "type" => DocumentType::FOLDER
        //     ]);
        DeleteUnverifiedUsersJob::dispatch($user)->delay(now()->addHours(24));

        Mail::to($user)->send(new VerifyEmailAddress($user));

        return response()->json("OK");
    } //end method register

    public function logout(Request $request)
    {
        auth()->user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json("OK");
    } //end method logout

    public function login(Request $request)
    {
        $request->validate([
            "username" => "string|required",
            "password" => "string|required",
        ]);

        $username = $request->username;
        $password = $request->password;

        $user = User::where("email", $username)->first();

        if ($user == null) {
            return response()->json(["message" => "User does not exist"], 404);
        }

        if (Auth::attempt(['email' => $username, 'password' => $password])) {
            $token = $user->createToken(env("PASSPORT_WEB_FRONTEND_TOKEN_NAME"));
            $returnData = [
                "token_type" => "Bearer",
                "access_token" => $token->accessToken,
            ];

            return response()->json($returnData, 200);
        }

        return response(["message" => "Wrong credentials"], 400);
    } //end method login

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            "nationality" => "string",
            "occupation" => "string",
            "marital_status" => "string",
            "gender" => "string",
            "phone_number" => "string",
            "about" => "string",
            "address" => "string",
            "name" => "string",
            "email" => "string",
            "dob" => "string",
            "display_photo" => "mimes:jpg,jpeg,png",
            "cover_image" => "mimes:jpg,jpeg,png",
            "job_title" => "string",
        ]);

        $data = $request->only([
            "nationality",
            "occupation",
            "marital_status",
            "gender",
            "phone_number",
            "about",
            "address",
            "name",
            "email",
            "dob",
            "job_title",
        ]);

        $user = auth()->user();
        $entity = $user->entity;

        $forUser = $request->only([
            "address",
            "name",
            "email",
            "job_title",
        ]);

        $forEntity = [];
        $errors = array();

        if ($user->entity_type == Person::class) {
            $forEntity = $request->only([
                "nationality",
                "occupation",
                "marital_status",
                "gender",
                "phone_number",
                "about",
            ]);

            if ($request->has("dob")) {
                $forEntity[] = ["dob", Carbon::create((string) $request->dob)];
            }

            //Validate regex
            //name validation
            if ($request->has("name")) {
                $name = preg_replace("/\s+/", " ", trim($request->name));
                $nameRegexPattern = "/^([a-zA-Z\x{00C0}-\x{00FF}]+( [a-zA-Z\x{00C0}-\x{00FF}]+)+)$/";
                if (!preg_match($nameRegexPattern, $name)) {
                    $errors["name"][] = "Invalid name input";
                    $errors["name"][] = "At least two names must be given";
                    $errors["name"][] = "Letters and spaces only";
                }
            }
        }

        //check for errors
        if (count($errors) > 0) {
            return response()->json([
                "message" => "Invalid input values",
                "errors" => $errors,
            ], 406);
        }

        $user->fill($forUser);
        $entity->fill($forEntity);

        if ($request->has("display_photo")) {
            $formerDisplayPhoto = $user->display_photo;

            $user->display_photo = Storage::url(Storage::disk("public")->put("display_photos", $request->file("display_photo")));
            $data["display_photo"] = $user->display_photo;
            try {
                unlink(public_path() . $formerDisplayPhoto);
            } catch (\Exception $e) {}
        } //end method display_photo

        if ($request->has("cover_image")) {
            $formerCoverImage = $user->cover_image;
            $user->cover_image = Storage::url(Storage::disk("public")->put("cover_images", $request->file("cover_image")));
            $data["cover_image"] = $user->cover_image;

            try {
                unlink(public_path() . $formerCoverImage);
            } catch (\Exception $e) {}
        } //end method display_photo

        $user->save();
        $entity->save();

        if ($request->has("display_photo")) {
            ResizeProfilePhoto::dispatch($user);
        }

        if ($request->has("cover_image")) {
            ResizeCoverPhoto::dispatch($user);
        }

        return response()->json($data);

    } //end method update

    public function suggestUsername($email, $username = null)
    {
        if ($username == null) {
            $username = $email;
        }

        $username = \preg_replace('/\s+/', '', $username);

        $values = [$username, $email];

        $array = array();
        $iterations = 1;
        do {
            $sug = preg_replace('/([^@]*)(.*)/', '$1', $values[rand(0, count($values) - 1)]);
            $sug = preg_replace('/^[^A-Za-z]+/', '', $sug);
            $sug = preg_replace('/[^A-Za-z\-\'0-9 ]/', '', $sug);

            $x = rand(0, 1);

            $sug = $x == 0 ? $sug . rand(1, 99) : $sug;

            if (User::where('username', $sug)->count() == 0) {
                if (!in_array($sug, $array)) {
                    $array[] = $sug;
                    break;
                }
            }
            $iterations++;
        } while (true);

        return $array[0];
    } //end function suggestUsernames

    public function verifyEmail(Request $request, User $user)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        $user->email_verified_at = now();

        $user->save();

        Mail::to($user)->send(new Welcome($user));

        return redirect(config("web_client_details.url"));
        // return "Email verified!!";
    } //end method verifyEmail
}
