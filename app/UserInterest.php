<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Traits\UsesUuid;

class UserInterest extends Pivot
{
    use UsesUuid;
    protected $guarded = [];

    protected $table = "user_interests";
}//end class UserInterest
