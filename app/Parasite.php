<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

use App\Traits\UsesUuid;

class Parasite extends Pivot
{

    use UsesUuid;
    
    protected $table = "parasites";
    protected $guarded = [];
}//end class Parasie
