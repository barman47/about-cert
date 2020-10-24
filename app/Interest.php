<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class Interest extends Model
{
    use UsesUuid;

    protected $guarded = [];

    public function users(){
        return $this->belongsToMany(User::class, "user_interests", "interest_id", "user_id")
                    ->using(UserInterest::class)
                    ->withTimestamps();
    }//end method users
}
