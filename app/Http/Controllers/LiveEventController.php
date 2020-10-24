<?php

namespace App\Http\Controllers;

use App\Exceptions\LiveEventRepositoryException;
use App\Repositories\LiveEventRepository;
use Illuminate\Http\Request;

use App\User;

class LiveEventController extends Controller
{
    public function store(Request $request, LiveEventRepository $liveEventRepository)
    {
        $request->validate([
            "title" => "string|required",
            "description" => "string",
            "can_join" => "string|required",
            "cover_image" => "image|required",
        ]);

        $user = auth()->user();

        $canJoin = json_decode($request->can_join);

        if (gettype($canJoin) != "array") {
            return response()->json([
                "message" => "Invalid can_join field, only stringified json arrays are allowed",
                "errors" => [
                    "can_join" => ["Invalid can_join field, only stringified json arrays are allowed"],
                ],
            ], 422);
        }

        try {
            $liveEvent = $liveEventRepository
                ->user($user)
                ->title($request->title)
                ->description($request->description ?? "")
                ->canJoin($canJoin)
                ->coverImage($request->file("cover_image"))
                ->create();
        } catch (LiveEventRepositoryException $e) {
            $message = $e->getMessage() ?? "An error occured";
            $code = $e->getCode() == 0 ? 400 : $e->getCode();

            return response()->json(["message" => $message], $code);
        }

        return response()->json(["id" => $liveEvent->id], 201);
    } //end method store

    public function getLiveEvents(LiveEventRepository $liveEventRepository)
    {
        $user = auth()->user();
        try {
            $paginatedLiveEvents = $liveEventRepository->user($user)->paginatedLiveEvents();
        } catch (LiveEventRepositoryException $e) {
            $message = $e->getMessage() ?? "An error occured";
            $code = $e->getCode() == 0 ? 400 : $e->getCode();

            return response()->json(["message" => $message], $code);
        }

        return response()->json(["data" => $paginatedLiveEvents]);
    } //end method getLiveEvents

    public function getSingleLiveEvent($id, LiveEventRepository $liveEventRepository)
    {
        $user = auth()->user();

        $liveEvent = $liveEventRepository->getLiveEventById($id);

        if($liveEvent == null)
            return response()->json(["message" => "Live event not found"], 404);

        try {
            $liveEvent = $liveEventRepository->user($user)->liveEvent($liveEvent)->getSingleLiveEventData();
        } catch (LiveEventRepositoryException $e) {
            $message = $e->getMessage() ?? "An error occured";
            $code = $e->getCode() == 0 ? 400 : $e->getCode();

            return response()->json(["message" => $message], $code);
        }

        return response()->json(["data" => $liveEvent]);
    } //end method getLiveEvents

    public function getMessages($id, Request $request, LiveEventRepository $liveEventRepository)
    {
        $user = auth()->user();
        $liveEvent = $liveEventRepository->getLiveEventById($id);

        try {
            $paginatedLiveEventMessages = $liveEventRepository->user($user)->liveEvent($liveEvent)->paginatedLiveEventMessages();
        } catch (LiveEventRepositoryException $e) {
            $message = $e->getMessage() ?? "An error occured";
            $code = $e->getCode() == 0 ? 400 : $e->getCode();

            return response()->json(["message" => $message], $code);
        }

        return response()->json(["data" => $paginatedLiveEventMessages]);
    } //end method getMessages

    public function startSession(Request $request, LiveEventRepository $liveEventRepository)
    {
        $request->validate([
            "live_event_id" => "required|string",
        ]);

        $user = auth()->user();
        $liveEvent = $liveEventRepository->getLiveEventById($request->live_event_id);

        try {
            $liveEventRepository
                ->user($user)
                ->liveEvent($liveEvent)
                ->startSession();
        } catch (LiveEventRepositoryException $e) {
            $message = $e->getMessage() ?? "An error occured";
            $code = $e->getCode() == 0 ? 400 : $e->getCode();

            return response()->json(["message" => $message], $code);
        }

        return response()->json(["message" => "Session started"], 200);
    } //end method startSession

    public function endSession(Request $request, LiveEventRepository $liveEventRepository)
    {
        $request->validate([
            "live_event_id" => "required|string",
        ]);

        $user = auth()->user();
        $liveEvent = $liveEventRepository->getLiveEventById($request->live_event_id);

        try {
            $liveEventRepository
                ->user($user)
                ->liveEvent($liveEvent)
                ->endSession();
        } catch (LiveEventRepositoryException $e) {
            $message = $e->getMessage() ?? "An error occured";
            $code = $e->getCode() == 0 ? 400 : $e->getCode();

            return response()->json(["message" => $message], $code);
        }

        return response()->json(["message" => "Session terminated"], 200);
    } //end method endSession

    public function sendMessage(Request $request, LiveEventRepository $liveEventRepository)
    {
        $request->validate([
            "content" => "string|required",
            "live_event_id" => "string|required",
        ]);

        $user = auth()->user();
        $liveEvent = $liveEventRepository->getLiveEventById($request->live_event_id);

        try {
            $liveEventMessage = $liveEventRepository
                ->user($user)
                ->liveEvent($liveEvent)
                ->sendMessageToLiveEvent($request->content);
        } catch (LiveEventRepositoryException $e) {
            $message = $e->getMessage() ?? "An error occured";
            $code = $e->getCode() == 0 ? 400 : $e->getCode();

            return response()->json(["message" => $message], $code);
        }

        return response()->json(["id" => $liveEventMessage->id]);
    } //end method sendMessage

    //========================================================================
    //=========================== FOR WEBRTC
    //========================================================================

    public function sendOffer(Request $request, LiveEventRepository $liveEventRepository)
    {
        $request->validate([
            "to" => "required|string",
            "offer" => "required|string",
            "on" => "required|string",
        ]);

        $user = auth()->user();
        $receiver = User::find($request->to);
        $liveEvent = $liveEventRepository->getLiveEventById($request->on);

        if ($liveEvent == null) {
            return response()->json(["message" => "Unable to find live event"], 404);
        }

        if ($receiver == null) {
            return response()->json(["message" => "Unable to find user"], 404);
        }

        try {
            $liveEventRepository->user($user)->liveEvent($liveEvent)->sendWebRtcOffer($receiver, $request->offer);
        } catch (LiveEventRepositoryException $e) {
            $message = $e->getMessage() ?? "An error occured";
            $code = $e->getCode() == 0 ? 400 : $e->getCode();

            return response()->json(["message" => $message], $code);
        }

        return response()->json(["message" => "OK"]);
    } //end method sendOffer

    public function sendAnswer(Request $request, LiveEventRepository $liveEventRepository)
    {
        $request->validate([
            "to" => "required|string",
            "answer" => "required|string",
            "on" => "required|string",
        ]);

        $user = auth()->user();
        $receiver = User::find($request->to);
        $liveEvent = $liveEventRepository->getLiveEventById($request->on);

        if ($liveEvent == null) {
            return response()->json(["message" => "Unable to find live event"], 404);
        }

        if ($receiver == null) {
            return response()->json(["message" => "Unable to find user"], 404);
        }

        try {
            $liveEventRepository->user($user)->liveEvent($liveEvent)->sendWebRtcAnswer($receiver, $request->answer);
        } catch (LiveEventRepositoryException $e) {
            $message = $e->getMessage() ?? "An error occured";
            $code = $e->getCode() == 0 ? 400 : $e->getCode();

            return response()->json(["message" => $message], $code);
        }

        return response()->json(["message" => "OK"]);
    } //end method sendAnswer

    public function sendICECandidate(Request $request, LiveEventRepository $liveEventRepository)
    {
        $request->validate([
            "to" => "required|string",
            "icecandidate" => "required|string",
            "on" => "required|string",
        ]);

        $user = auth()->user();
        $receiver = User::find($request->to);
        $liveEvent = $liveEventRepository->getLiveEventById($request->on);

        if ($liveEvent == null) {
            return response()->json(["message" => "Unable to find live event"], 404);
        }

        if ($receiver == null) {
            return response()->json(["message" => "Unable to find user"], 404);
        }

        try {
            $liveEventRepository->user($user)->liveEvent($liveEvent)->sendWebRtcICECandidate($receiver, $request->icecandidate);
        } catch (LiveEventRepositoryException $e) {
            $message = $e->getMessage() ?? "An error occured";
            $code = $e->getCode() == 0 ? 400 : $e->getCode();

            return response()->json(["message" => $message], $code);
        }

        return response()->json(["message" => "OK"]);
    } //end method sendICECandidate
} //end class LiveEventController
