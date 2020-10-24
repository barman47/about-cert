<?php
namespace App\Exceptions;

use Exception;
use Throwable;

class PaymentRepositoryException extends Exception{
    public function __construct(string $message, int $code = 0, Throwable $previous = NULL){
        parent::__construct($message, $code, $previous);
    }//end constructor
}//end class PaymentRepositoryException

