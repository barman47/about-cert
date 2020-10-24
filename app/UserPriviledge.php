<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Traits\UsesUuid;

class UserPriviledge extends Pivot
{
    use UsesUuid;

    protected $guarded = [];
    protected $table = "user_priviledges";
}//end class UserPriviledge
