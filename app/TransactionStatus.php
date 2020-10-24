<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

abstract class TransactionStatus extends Model
{
    const PENDING = "pending";
    const FAILED = "failed";
    const SUCCESS = "success";
}//end abstract class TransactionStatus
