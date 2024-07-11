<?php

namespace App\Plentific\Api;

use GuzzleHttp\Client;

abstract class BaseApi
{
    protected Client $client;

    // Would like to derive this from an env/config file in a later refactor
    protected string $baseUrl = 'https://reqres.in/api/';

     public function __construct()
     {
         $this->client = new Client(['base_uri' => $this->baseUrl]);
     }

     protected abstract function handleErrorResponse(int $code): void;
}
