<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class SignaturePriviledge extends Model
{
    protected $guarded = [];

    use UsesUuid;
}//end class SignaturePriviledge
