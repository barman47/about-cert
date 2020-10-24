<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;


class Alert extends Model
{
    use UsesUuid;

    protected $guarded = [];
}//end class Alert
