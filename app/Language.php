<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Traits\UsesUuid;

class Language extends Model
{
    use UsesUuid;

    protected $guarded = [];

    public function users(){
        return $this->belongsToMany(User::class, "user_languages", "language_id", "used_id")
                    ->using(UserLanguage::class)
                    ->withTimestamps()
                    ->withPivot('proficiency');
    }//end method users
}//end class Languafe
