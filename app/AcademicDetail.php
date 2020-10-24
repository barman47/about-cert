<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Traits\UsesUuid;

class AcademicDetail extends Model
{
    use UsesUuid;
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }//end method user
}//end class AcademicDetail
