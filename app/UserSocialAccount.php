<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class UserSocialAccount extends Model
{
    use UsesUuid;

    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }//end method user
}//end class UserSocialAccount
