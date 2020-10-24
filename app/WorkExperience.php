<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class WorkExperience extends Model
{
    use UsesUuid;

    protected $guarded = [];

    protected $table = "work_experiences";

    public function user(){
        return $this->belongsTo(User::class);
    }//end method user
}//end class WorkExperience
