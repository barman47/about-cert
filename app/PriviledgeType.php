<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use File;

abstract class PriviledgeType extends Model
{
   const TYPES = [
      "access" => "access",
      "template" => 'template',
      "template_group" => "template_group",
      "signature" => "signature",
      "opportunity_maker" => "opportunity_maker"
   ];
}
