<?php
namespace App\Exceptions;

use Exception;
use Throwable;

class PDFGeneratorException extends Exception{
    public function __construct(string $message, int $code = 0, Throwable $previous = NULL){
        parent::__construct($message, $code, $previous);
    }//end constructor
}//end class PDFGeneratorException
