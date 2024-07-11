<?php

namespace App\Plentific\Exceptions;


class ApiException extends \Exception
{
    protected $code = 500;
    protected $message = 'Unknown API error has occurred.';
}
