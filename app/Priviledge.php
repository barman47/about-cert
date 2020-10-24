<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class Priviledge extends Model
{
    use UsesUuid;

    protected $guarded = [];

    public function users(){
        return $this->belongsToMany(User::class, "user_priviledges", "priviledge_id", "user_id")
                    ->using(UserPriviledge::class)
                    ->withTimestamps()
                    ->withPivot([
                        "meta",
                        "target_id"
                    ]);
    }//end method users
}//end class Priviledge
