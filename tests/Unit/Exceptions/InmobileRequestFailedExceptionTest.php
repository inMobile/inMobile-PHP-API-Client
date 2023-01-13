<?php

namespace Inmobile\Tests\Unit\Exceptions;

use Inmobile\InmobileSDK\Exceptions\InmobileRequestFailedException;
use Inmobile\InmobileSDK\Response;
use PHPUnit\Framework\TestCase;

class InmobileRequestFailedExceptionTest extends TestCase
{
    protected function invalidResponse()
    {
        return <<<JSON
        {
          "errorMessage": "Something went wrong",
          "details": [
            "string"
          ]
        }
        JSON;
    }

    public function test_get_response()
    {
        $response = new Response($this->invalidResponse(), 200);
        $exception = new InmobileRequestFailedException($response);

        $this->assertInstanceOf(Response::class, $exception->getResponse());
    }

    public function test_create_from_response()
    {
        $response = new Response($this->invalidResponse(), 200);
        $exception = InmobileRequestFailedException::fromResponse($response);

        $this->assertInstanceOf(InmobileRequestFailedException::class, $exception);
    }

    public function test_handles_empty_response_bodies()
    {
        $response = new Response('', 500);
        $exception = InmobileRequestFailedException::fromResponse($response);

        $this->assertInstanceOf(InmobileRequestFailedException::class, $exception);
        $this->assertEquals('The request failed with an empty response and status code: 500', $exception->getMessage());
    }
}
