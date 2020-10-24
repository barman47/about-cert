<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserCV extends Pivot
{
    use UsesUuid;
    protected $guarded = [];

    protected $table = "user_c_v_s";
}//end class UserCV
