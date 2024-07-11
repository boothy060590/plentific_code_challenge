<?php

namespace App\Plentific\Exceptions;

class InvalidApiResponseException extends ApiException
{
    protected $code = 422; // unprocessable entity
    protected $message = 'Invalid API response structure';
}
