<?php

namespace App\Repositories;

use App\Events\LiveEventSendOfferEvent;
use App\Events\LiveEventSendAnswerEvent;
use App\Events\LiveEventSendICECandidateEvent;

use App\Exceptions\LiveEventMessageRepositoryException;
use App\Exceptions\LiveEventRepositoryException;
use App\Jobs\ResizeLiveEventPhoto;
use App\Events\LiveEventCreated;
use App\LiveEvent;
use App\LiveEventMessage;
use App\User;
use App\Parasite;
use DB;
use Storage;

class LiveEventRepository
{
    private $user;
    private $liveEvent;
    private $coverImageFile;

    public function __construct()
    {
        $this->liveEvent = new LiveEvent();
    } //end constructor method

    public function user(User $user): LiveEventRepository
    {
        $this->user = $user;
        return $this;
    } //end method user

    public function liveEvent(LiveEvent $liveEvent): LiveEventRepository
    {
        $this->liveEvent = $liveEvent;
        return $this;
    } //end method liveEvent

    public function title(string $title): LiveEventRepository
    {
        $this->liveEvent->title = $title;
        return $this;
    } //end method title

    public function numberOfActiveMembers($numberOfActiveMembers): LiveEventRepository
    {
        $this->liveEvent->number_of_active_members = $numberOfActiveMembers;
        return $this;
    } //end method numberOfActiveMembers

    public function isInSession($isInSession): LiveEventRepository
    {
        $this->liveEvent->is_in_session = $isInSession ? 1 : 0;
        return $this;
    } //end method isInSession

    public function description(string $description): LiveEventRepository
    {
        $this->liveEvent->description = $description;
        return $this;
    } //end method description

    public function canJoin(array $canJoin): LiveEventRepository
    {
        if (count($canJoin) == 0) {
            $canJoin = ["all"];
        }

        $this->liveEvent->can_join = json_encode($canJoin);
        return $this;
    } //end method canView

    public function coverImage($coverImageFile): LiveEventRepository
    {
        $this->coverImageFile = $coverImageFile;
        return $this;
    } //end method coverImage

    public function save(): LiveEvent
    {
        if (!$this->liveEvent) {
            throw new LiveEventRepositoryException("The Live Event must be specified", 403);
        }
        $this->handleSaveCoverImage();
        $this->liveEvent->save();

        $this->resizeLiveEventCoverImage();

        return $this->liveEvent;
    } //end method save

    public function create(): LiveEvent
    {
        if (!$this->user) {
            throw new LiveEventRepositoryException("The User must be specified", 403);
        }

        $this->handleSaveCoverImage();
        $this->user->liveEvents()->save($this->liveEvent);
        $this->resizeLiveEventCoverImage();
        $this->liveEvent->refresh();
        broadcast(new LiveEventCreated($this->liveEvent));
        return $this->liveEvent;
    } //end method create

    public function getLiveEventById($id): LiveEvent
    {
        return LiveEvent::find($id);
    } //end method getLiveEventById

    public function canJoinLiveEvent(): bool
    {
        $liveEvent = $this->liveEvent;
        $user = $this->user;

        if($liveEvent->user_id == $user->id){
            return true;
        }

        $canJoin = json_decode($liveEvent->can_join);

        $channels = [];
        $userIds = [];

        if(in_array("all", $canJoin)){
            return true;
        }

        foreach($canJoin as $value){
            if($value == "followers"){
                $userIds = array_merge($userIds, Parasite::where("host", $user->id)->pluck("parasite")->toArray());
                continue;
            }

            $userIds[] = $value;
        }

        if(in_array($user->id, $userIds))
            return true;

        return false;
    } //end method canJoinLiveEvent

    public function paginatedLiveEvents()
    {
        if (!$this->user) {
            throw new LiveEventRepositoryException("The User must be specified", 403);
        }

        $user = $this->user;

        $paginatedLiveEvents = LiveEvent::
            where(function ($query) {
                $query->whereJsonContains("can_join", ["all"])
                    ->orWhereJsonContains("can_join", [$this->user->id]);
            })
            ->orWhere(function ($query) {
                $query->whereJsonContains("can_join", ["followers"])
                    ->whereExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('parasites')
                            ->whereRaw('parasites.host = live_events.user_id')
                            ->whereRaw('parasites.parasite = \'' . $this->user->id . '\'');
                    });
            })
        //TODO: uncomment next line
        // ->where("is_in_session", 1)
            ->latest()
            ->paginate();
        return $paginatedLiveEvents;
    } //end method paginatedLiveEvents

    public function getSingleLiveEventData(){
        if (!$this->canJoinLiveEvent()) {
            throw new LiveEventRepositoryException("The user does not have the right to perform action", 403);
        }

        return $this->liveEvent;
    }//end method getSingleLiveEvent

    public function paginatedLiveEventMessages(){
        if (!$this->canJoinLiveEvent()) {
            throw new LiveEventRepositoryException("The user does not have the right to perform action", 403);
        }

        $liveEventMessageRepository = new LiveEventMessageRepository();
        try {
            return $liveEventMessageRepository->user($this->user)->liveEvent($this->liveEvent)->paginatedLiveEventMessages();
        } catch (LiveEventMessageRepositoryException $e) {
            throw new LiveEventRepositoryException($e->getMessage(), $e->getCode());
        }
    }//end method paginatedLiveEventMessages

    public function startSession()
    {
        if (!$this->user) {
            throw new LiveEventRepositoryException("The User must be specified", 403);
        }
        $user = $this->user;
        $liveEvent = $this->liveEvent;

        if ($user->id != $liveEvent->user_id) {
            throw new LiveEventRepositoryException("The user does not have the right to perform action", 403);
        }

        $this->isInSession(1)->save();

        //TODO: broadcast the live event to all user
    } //end method startSession

    public function endSession()
    {
        if (!$this->user) {
            throw new LiveEventRepositoryException("The User must be specified", 403);
        }
        $user = $this->user;
        $liveEvent = $this->liveEvent;

        if ($user->id != $liveEvent->user_id) {
            throw new LiveEventRepositoryException("The user does not have the right to perform action", 403);
        }

        $this->isInSession(0)->save();
    } //end method endSession

    public function sendMessageToLiveEvent(string $content): LiveEventMessage
    {
        if (!$this->canJoinLiveEvent()) {
            throw new LiveEventRepositoryException("The user does not have the right to perform action", 403);
        }

        $liveEventMessageRepository = new LiveEventMessageRepository();
        try {
            return $liveEventMessageRepository->user($this->user)->liveEvent($this->liveEvent)->content($content)->create();

        } catch (LiveEventMessageRepositoryException $e) {
            throw new LiveEventRepositoryException($e->getMessage(), $e->getCode());
        }
    } //end method sendMessageToliveEvent

    private function resizeLiveEventCoverImage()
    {
        if ($this->coverImageFile) {
            ResizeLiveEventPhoto::dispatch($this->liveEvent);
        }

    } //end method resizeLiveEventCoverImage

    private function handleSaveCoverImage(): string
    {
        if (!$this->coverImageFile) {
            return "";
        }

        $temp = "";

        $temp = $this->liveEvent->cover_image;
        $this->liveEvent->cover_image = Storage::url(Storage::disk("public")->put("live_events/cover_images", $this->coverImageFile));

        if ($temp) {
            unlink(public_path($temp));
        }

        return $this->liveEvent->cover_image;
    } //end method handleSaveCoverImage

    public function sendWebRtcOffer(User $receiver, $offer){
        if (!$this->canJoinLiveEvent()) {
            throw new LiveEventRepositoryException("The user does not have the right to perform action", 403);
        }

        if (!$this->user) {
            throw new LiveEventRepositoryException("The User must be specified", 403);
        }

        broadcast(new LiveEventSendOfferEvent($this->user, $receiver, $this->liveEvent, $offer))->toOthers();
    }//end method sendWebRtcOffer

    public function sendWebRtcAnswer(User $receiver, $answer){
        if (!$this->canJoinLiveEvent()) {
            throw new LiveEventRepositoryException("The user does not have the right to perform action", 403);
        }

        if (!$this->user) {
            throw new LiveEventRepositoryException("The User must be specified", 403);
        }

        broadcast(new LiveEventSendAnswerEvent($this->user, $receiver, $this->liveEvent, $answer))->toOthers();
    }//end method sendWebRtcAnswer

    public function sendWebRtcICECandidate(User $receiver, $icecandidate){
        if (!$this->canJoinLiveEvent()) {
            throw new LiveEventRepositoryException("The user does not have the right to perform action", 403);
        }

        if (!$this->user) {
            throw new LiveEventRepositoryException("The User must be specified", 403);
        }

        broadcast(new LiveEventSendICECandidateEvent($this->user, $receiver, $this->liveEvent, $icecandidate))->toOthers();
    }//end method sendWebRtcAnswer
} //end class LiveEventRepository
