<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Traits\UsesUuid;

class Country extends Model
{
    use UsesUuid;
    protected $guarded = [];
}//end class Country
