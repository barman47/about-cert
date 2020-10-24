<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class Company extends Model
{
    use UsesUuid;

    protected $guarded = [];

    public function root(){
        return $this->morphOne(User::class, "entity");
    }//end method 
}//end class Company
