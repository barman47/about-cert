<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

use App\Traits\CustomPaginate;

class CVTemplateGroup extends Model
{
    use UsesUuid, CustomPaginate;
    protected $guarded = [];

    public function templates(){
        return $this->hasMany(CVTemplate::class);
    }//end method templates

    /**
     * For the custom pagination
     */

    private function customPaginateDataBuilder(){
        return static::whereNotIn('group_code', ['tailored'])->whereHas("templates")->latest()->with("templates");
    }//end method customPaginateDataBuilder

    private function customPaginateDataCount(){
        return static::whereNotIn('group_code', ['tailored'])->whereHas("templates");
    }//end method customPaginateDataCount
}//end class CVTemplateGroup
