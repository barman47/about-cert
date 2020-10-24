<?php
namespace App\Repositories;


use App\User;
use App\Priviledge;

use App\Exceptions\PriviledgeRepositoryException;
use App\UserPriviledge;

class PriviledgeRepository{
    private $user;
    private $priviledge;
    private $meta;
    private $targetId;

    public function user(User $user) : PriviledgeRepository
    {
        $this->user = $user;
        return $this;
    }//end method user

    public function priviledge(Priviledge $priviledge): PriviledgeRepository
    {
        $this->priviledge = $priviledge;
        return $this;
    }//end method priviledge

    public function target($targetId) : PriviledgeRepository
    {
        $this->targetId = $targetId;
        return $this;
    }//end method target

    public function meta($meta): PriviledgeRepository{
        if(!(gettype($meta) == "array" ||
            gettype($meta) == "object" ||
            (
                gettype($meta) == "string" &&
                !is_null(json_decode($meta))
            )
        )){
            throw new PriviledgeRepositoryException("'meta' not a valid array, object or json string");
        }
        $this->meta = serialize($meta);
        return $this;
    }//end method include

    public function getMeta(){
        if($this->meta)
            return unserialize($this->meta);

        $rv = null;

        if($this->user && $this->priviledge){
            $pivot = $this->user->pivot ?? $this->priviledge->pivot;
            if($pivot && $pivot->priviledge_id == $this->priviledge->id && $pivot->user_id == $this->user->id){
                if($pivot->target_id == null)
                    $rv = $pivot->meta;
                elseif($pivot->target_id == $this->targetId){
                    $rv = $pivot->meta;
                }
            }

            if($rv == null){
                $userPriviledge = UserPriviledge::where([
                    ["user_id", $this->user->id],
                    ["priviledge_id", $this->priviledge->id],
                    ["target_id", $this->targetId]
                ])->select("meta")->first();

                if($userPriviledge != null){
                    if($userPriviledge->meta == null)
                        return null;

                    $rv = $userPriviledge->meta;
                }
            }
        }else{
            throw new PriviledgeRepositoryException("Provide User And Priviledge");
        }

        if($rv == null){
            throw new PriviledgeRepositoryException("An Error Occured while Getting meta");
        }else{
            $this->meta = $rv;
            
            return unserialize($rv);
        }

    }//end method getMeta

    public function create(): PriviledgeRepository
    {
        if($this->user == null || $this->priviledge == null)
            throw new PriviledgeRepositoryException("The user or the priviledge object not defined");

        if($this->user->priviledges()->where("priviledges.id", $this->priviledge->id)->first() == null ||
            !($this->targetId && $this->user->priviledges()->where("priviledges.id", $this->priviledge->id)->wherePivot("target_id", $this->targetId)->first() )
            ){
            $this->user->priviledges()->attach($this->priviledge->id, [
                "meta" => $this->meta,
                "target_id" => $this->targetId
            ]);
        }

        return $this;
    }//end method create

    public function destroy(): PriviledgeRepository{
        if($this->user == null || $this->priviledge == null)
            throw new PriviledgeRepositoryException("The user or the priviledge object not defined");

        if($this->user->priviledges()->where("priviledges.id", $this->priviledge->id)->wherePivot("target_id", $this->targetId)->first() != null){
            if(!$this->targetId)
                $this->user->priviledges()->detach($this->priviledge->id);
            else{
                UserPriviledge::where([
                        ["user_id", $this->user->id],
                        ["priviledge_id", $this->priviledge->id],
                        ["target_id", $this->targetId]
                    ])->first()->delete();
            }
        }else{
            throw new PriviledgeRepositoryException("There is no connection between the user and priviledge");
        }

        return $this;
    }//end method destroy

    public function update(): PriviledgeRepository
    {
        if($this->user == null || $this->priviledge == null)
            throw new PriviledgeRepositoryException("The user or the priviledge object not defined");

        if($this->user->priviledges()->where("priviledges.id", $this->priviledge->id)->first() != null){
            $updateValues = [];

            if($this->meta){
                $updateValues["meta"] = $this->meta;
            }
            if($this->targetId){
                $updateValues["target_id"] = $this->targetId;
            }

            $this->user->priviledges()->upDateExistingPivot($this->priviledge->id, $updateValues);
        }else{
            throw new PriviledgeRepositoryException("There is no connection between the user and the priviledge");
        }

        return $this;
    }//end method update



    public function hasPriviledge(): bool
    {
        if($this->user == null || $this->priviledge == null)
            throw new PriviledgeRepositoryException("The user or the priviledge object not defined");

        if($this->user->priviledges()->where("priviledges.id", $this->priviledge->id)->first() == null)
            return false;

        if($this->targetId){
            if($this->user->priviledges()->where("priviledges.id", $this->priviledge->id)->first()->pivot->target_id != $this->targetId)
                return false;
        }

        return true;
    }//end method hasPriviledge
}//end class PriviledgeRepository
