<?php

namespace App\Repositories;

use Storage;
use DB;

use App\Exceptions\LiveEventMessageRepositoryException;
use App\LiveEventMessage;
use App\LiveEvent;
use App\User;

class LiveEventMessageRepository{
	private $user;
	private $liveEvent;
	private $liveEventMessage;

	public function __construct(){
		$this->liveEventMessage = new LiveEventMessage();
	}//end constructor method

	public function user(User $user): LiveEventMessageRepository{
		$this->user = $user;
		return $this;
	}//end method user

	public function liveEvent(LiveEvent $liveEvent): LiveEventMessageRepository{
		$this->liveEvent = $liveEvent;
		return $this;
	}//end method liveEvent

	public function liveEventMessage(LiveEventMessage $liveEventMessage): LiveEventMessageRepository{
		$this->liveEventMessage = $liveEventMessage;
		return $this;
	}//end method liveEventMessage

	public function content(string $content): LiveEventMessageRepository {
		$this->liveEventMessage->content = $content;
		return $this;
	}//end method content

	public function create(): LiveEventMessage{
		if(!$this->user->id || !$this->liveEvent->id){
			throw new LiveEventMessageRepositoryException("The user and the live event must be specified", 400);
		}

		$user = $this->user;
		$liveEventMessage = $this->liveEventMessage;
		$liveEvent = $this->liveEvent;

		$liveEventMessage->sender_id = $user->id;
		$liveEventMessage->live_event_id = $liveEvent->id;

		$liveEventMessage->save();
		return $liveEventMessage;
	}//end method create


	public function paginatedLiveEventMessages(){
		if(!$this->user->id || !$this->liveEvent->id){
			throw new LiveEventMessageRepositoryException("The user and the live event must be specified", 400);
		}

		return $this->liveEvent->messages()->latest()->with("sender:id,name,thumbnail")->paginate(30);
	}//end method paginatedLiveEventMessages
}//end class LiveEventMessageRepository