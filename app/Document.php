<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

use App\Traits\UsesUuid;
use App\Traits\DocumentEvents;

class Document extends Model
{
    use UsesUuid, DocumentEvents, Searchable;
    protected $guarded = [];
    const SOFT_DELETE_PERIOD_IN_DAYS = 30;

    public function toSearchableArray()
    {
        $array = $this->toArray();

        return $array;
    }//end method toSearchableArray


    
    public static function setUpStyles($pw){
        $pw->addFontStyle("Heading1", ["size" => 32, "bold" => true]);
        $pw->addFontStyle("Heading2", ["size" => 24, "bold" => true]);
        $pw->addFontStyle("Heading3", ["size" => 19, "bold" => true]);
        $pw->addFontStyle("Heading4", ["size" => 16, "bold" => true]);
        $pw->addFontStyle("Heading5", ["size" => 13, "bold" => true]);
        $pw->addFontStyle("Heading6", ["size" => 11, "bold" => true]);

        $pw->addTitleStyle(1, ["size" => 32]);
        $pw->addTitleStyle(2, ["size" => 24]);
        $pw->addTitleStyle(3, ["size" => 19]);
        $pw->addTitleStyle(4, ["size" => 16]);
        $pw->addTitleStyle(5, ["size" => 13]);
        $pw->addTitleStyle(6, ["size" => 11]);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }//end method user

    public function documentable(){
        return $this->morphTo();
    }//end method documentable

    public function documents(){
        return $this->morphMany(static::class, "documentable");
    }//end method documents


}//end class Document
