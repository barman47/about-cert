<?php

namespace App;

use App\Traits\UsesUuid;

use Illuminate\Database\Eloquent\Model;

class Hobby extends Model
{
    use UsesUuid;
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
