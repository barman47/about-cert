<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class CVTemplate extends Model
{
    use UsesUuid;
    protected $guarded = [];

    public function users(){
        return $this->belongsToMany(User::class, "user_c_v_s", "c_v_template_id",  "user_id")
                    ->using(UserCV::class)
                    ->withTimestamps();
    }//end method users

    public function group(){
        return $this->belongsTo(CVTemplateGroup::class, "c_v_template_group_id");
    }//end method group
}//end class CVTemplate
