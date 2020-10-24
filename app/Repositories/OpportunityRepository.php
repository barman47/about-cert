<?php
namespace App\Repositories;

use App\Exceptions\OpportunityRepositoryException;

use App\Opportunity;
use App\Priviledge;
use App\User;

class OpportunityRepository{
    private $user;
    private $content;
    private $title;
    private $link;

    public function user(User $user):OpportunityRepository {
        $this->user = $user;
        return $this;
    }//end method user

    public function content(string $content): OpportunityRepository{
        $this->content = $content;
        return $this;
    }//end method content
    public function title(string $title): OpportunityRepository{
        $this->title = $title;
        return $this;
    }//end method title


    public function link(string $link): OpportunityRepository{
        $this->link = $link;
        return $this;
    }//end method link

    public function create(): Opportunity{
        if(!$this->hasPriviledge()){
            throw new OpportunityRepositoryException("The defined user does not have the priviledge to create opportunity", 403);
        }

        $user = $this->user;

        $opportunity = $user->opportunities()->create([
            "content" => $this->content,
            "link"  => $this->link,
            "title" => $this->title,
        ]);

        return $opportunity;
    }//end method create

    public function fetchAll($with = null, $withCount = null, $options = []){
        $query = Opportunity::latest();

        if($with != null){
            $query->with($with);
        }
        
        if($withCount != null)
            $query->withCount($withCount);

        $user = $this->user;

        $opportunities = $query->get();

        if(in_array("liked", $options)){
            if($user != null){
                $opportunities->loadMissing("likes");
                $opportunities->each(function($opportunity) use(&$user){
                    $opportunity->liked = $opportunity->likes->where("user_id", $user->id)->count() > 0 ? 1 : 0;
                });

                if(!($with != null && gettype($with) == "array" && in_array("likes", $with)))
                    $opportunities->makeHidden("likes");
            }
        }

        return $opportunities;
    }//end method fetchAll

    public function hasPriviledge(): bool{
        $priviledgeRepository = new PriviledgeRepository();
        $priviledge = Priviledge::where([
            ["code", "opportunity_maker"]
        ])->first();

        return $priviledgeRepository->user($this->user)->priviledge($priviledge)->hasPriviledge();
    }//end method hasPriviledge
}//end class OpportunityRepository