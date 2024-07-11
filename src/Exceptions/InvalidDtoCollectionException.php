<?php

namespace App\Plentific\Exceptions;

use http\Env\Response;

class InvalidDtoCollectionException extends \Exception
{
    protected $code = 422;
    protected $message = 'Property must be an array of DTOs.';
}
