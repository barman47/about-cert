<?php
namespace App\Repositories;

use App\User;
use App\Priviledge;

class AdminUserRepository
{
    private $user;

    public function user(User $user): AdminUserRepository
    {
        $this->user = $user;
        return $this;
    } //end method user

    function grantAdminPriviledge(){
        $priviledgeRepository = new PriviledgeRepository();
        $priviledge = Priviledge::where([
            ["code", "admin"]
        ])->first();

        $priviledgeRepository->user($this->user)->priviledge($priviledge)->create();
    }//end method grantAdminPriviledge

    function hasAdminPriviledge(): bool{
        $priviledgeRepository = new PriviledgeRepository();
        $priviledge = Priviledge::where([
            ["code", "admin"]
        ])->first();

        return $priviledgeRepository->user($this->user)->priviledge($priviledge)->hasPriviledge();
    }//end method hasAdminPriviledge
} //end class AdminRequestRepository
