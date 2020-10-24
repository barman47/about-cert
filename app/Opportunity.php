<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;
use App\Traits\MultipleDocumentable;
use App\Traits\Commentable;
use App\Traits\Likeable;

use App\Interfaces\ShareableInterface;

class Opportunity extends Model implements ShareableInterface
{
    use UsesUuid, MultipleDocumentable, Commentable, Likeable;
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }//end method user

    public function shares(){
        return $this->morphMany(Share::class, "shareable");
    }//end method shares
}//end class Opportunity
