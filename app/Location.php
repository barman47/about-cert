<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class Location extends Model
{
    use UsesUuid;

    protected $guarded = [];

    public function trackable(){
        return $this->morphTo();
    }//end method trackable
}//end class Location
