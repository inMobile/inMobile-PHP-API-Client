<?php

namespace Inmobile\Tests\Unit;

use Inmobile\InmobileSDK\CurlClient;
use Inmobile\InmobileSDK\Exceptions\InmobileRequestFailedException;
use Inmobile\InmobileSDK\InmobileApi;
use Inmobile\InmobileSDK\Response;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class InmobileApiTest extends MockeryTestCase
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

    public function test_can_get()
    {
        $client = Mockery::mock(CurlClient::class);
        $api = new InmobileApi('example-token', $client);

        $client->shouldReceive('request')
            ->with('GET', '/example-url?key=value', null)
            ->andReturn(['[]', 200])
            ->once();

        $response = $api->get('/example-url', ['key' => 'value']);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_can_post()
    {
        $client = Mockery::mock(CurlClient::class);
        $api = new InmobileApi('example-token', $client);

        $client->shouldReceive('request')
            ->with('POST', '/example-url', ['key' => 'value'])
            ->andReturn(['[]', 200])
            ->once();

        $response = $api->post('/example-url', ['key' => 'value']);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_can_put()
    {
        $client = Mockery::mock(CurlClient::class);
        $api = new InmobileApi('example-token', $client);

        $client->shouldReceive('request')
            ->with('PUT', '/example-url', ['key' => 'value'])
            ->andReturn(['[]', 200])
            ->once();

        $response = $api->put('/example-url', ['key' => 'value']);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_can_delete()
    {
        $client = Mockery::mock(CurlClient::class);
        $api = new InmobileApi('example-token', $client);

        $client->shouldReceive('request')
            ->with('DELETE', '/example-url', ['key' => 'value'])
            ->andReturn(['[]', 200])
            ->once();

        $response = $api->delete('/example-url', ['key' => 'value']);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_returns_response_with_status_and_contents()
    {
        $client = Mockery::mock(CurlClient::class);
        $api = new InmobileApi('example-token', $client);

        $client->shouldReceive('request')
            ->with('POST', '/example-url', ['key' => 'value'])
            ->andReturn(['{"foo": "bar"}', 200])
            ->once();

        $response = $api->post('/example-url', ['key' => 'value']);

        $this->assertEquals(200, $response->getStatus());
        $this->assertEquals(['foo' => 'bar'], $response->toArray());
    }

    /**
     * @test
     * @dataProvider invalidResponses
     */
    public function test_throws_an_exception_if_the_response_code_is_not_successful($statusCode)
    {
        $client = Mockery::mock(CurlClient::class);
        $api = new InmobileApi('example-token', $client);

        $client->shouldReceive('request')
            ->with('POST', '/example-url', ['key' => 'value'])
            ->andReturn([$this->invalidResponse(), $statusCode])
            ->once();

        $this->expectException(InmobileRequestFailedException::class);

        $api->post('/example-url', ['key' => 'value']);
    }

    public function invalidResponses(): array
    {
        return [
            [400],
            [401],
            [404],
            [500],
        ];
    }
}
