<?php

namespace Inmobile\Tests\Unit;

use Inmobile\InmobileSDK\CurlClient;
use Inmobile\InmobileSDK\Exceptions\CurlException;
use PHPUnit\Framework\TestCase;

class CurlClientTest extends TestCase
{
    public function test_handles_generic_curl_errors()
    {
        $client = new CurlClient('foobar', 'https://non-existant-domain');

        $this->expectException(CurlException::class);
        $this->expectExceptionMessage('cURL error 6: Could not resolve host: non-existant-domain (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)');

        $client->request('GET', '/test');
    }
}
