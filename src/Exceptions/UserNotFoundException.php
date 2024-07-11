<?php

namespace App\Plentific\Exceptions;

class UserNotFoundException extends ApiException
{
    protected $code = 404; // Not Found
    protected $message = 'User not found.';
}
