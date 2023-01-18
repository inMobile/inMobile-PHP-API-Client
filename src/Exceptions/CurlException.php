<?php

namespace Inmobile\InmobileSDK\Exceptions;

use Exception;

class CurlException extends Exception
{
    public function __construct($curlErrorCode, $curlMessage)
    {
        parent::__construct('cURL error ' . $curlErrorCode . ': ' . $curlMessage . ' (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)');
    }
}
