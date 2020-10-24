<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

use App\Traits\UsesUuid;

class UserLanguage extends Pivot
{
    use UsesUuid;
    protected $table = "user_languages";
}
