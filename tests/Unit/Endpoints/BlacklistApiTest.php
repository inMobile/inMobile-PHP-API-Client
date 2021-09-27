<?php

namespace Inmobile\Tests\Unit\Endpoints;

use Inmobile\InmobileSDK\Endpoints\BlacklistApi;
use Inmobile\InmobileSDK\InmobileApi;
use Inmobile\InmobileSDK\Response;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class BlacklistApiTest extends MockeryTestCase
{
    protected function validListResponse($next = null)
    {
        $lastPage = $next ? 'false' : 'true';

        return <<<JSON
        {
          "entries": [
            {
              "numberInfo": {
                "countryCode": "45",
                "phoneNumber": "12345678"
              },
              "comment": "Some text provided when created",
              "id": "string",
              "created": "2001-02-30T14:50:23Z"
            }
          ],
          "_links": {
            "next": "{$next}",
            "isLastPage": {$lastPage}
          }
        }
        JSON;
    }

    public function test_get_all_from_blacklist()
    {
        $api = Mockery::mock(InmobileApi::class);
        $blacklistsApi = new BlacklistApi($api);

        $api->shouldReceive('get')
            ->with(
                '/blacklist',
                ['pageLimit' => 250]
            )
            ->andReturn(new Response($this->validListResponse('/v4/blacklist/foobar'), 200))
            ->once();

        $api->shouldReceive('get')
            ->with(
                '/blacklist/foobar',
                ['pageLimit' => 250]
            )
            ->andReturn(new Response($this->validListResponse(), 200))
            ->once();

        $data = $blacklistsApi->getAll();

        $this->assertIsArray($data);
        $this->assertCount(2, $data);
    }

    public function test_create_entry()
    {
        $api = Mockery::mock(InmobileApi::class);
        $blacklistsApi = new BlacklistApi($api);

        $api->shouldReceive('post')
            ->with(
                '/blacklist',
                [
                    'numberInfo' => [
                        'countryCode' => '45',
                        'phoneNumber' => '12345678',
                    ],
                    'comment' => 'Hello World'
                ]
            )
            ->andReturn(new Response('[]', 200))
            ->once();

        $response = $blacklistsApi->createEntry(45, 12345678, 'Hello World');

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_get_entry_by_id()
    {
        $api = Mockery::mock(InmobileApi::class);
        $blacklistsApi = new BlacklistApi($api);

        $api->shouldReceive('get')
            ->with(
                '/blacklist/123-INMBL',
            )
            ->andReturn(new Response('[]', 200))
            ->once();

        $response = $blacklistsApi->findEntryById('123-INMBL');

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_get_entry_by_number()
    {
        $api = Mockery::mock(InmobileApi::class);
        $blacklistsApi = new BlacklistApi($api);

        $api->shouldReceive('get')
            ->with(
                '/blacklist/ByNumber',
                [
                    'countryCode' => '45',
                    'phoneNumber' => '12345678',
                ]
            )
            ->andReturn(new Response('[]', 200))
            ->once();

        $response = $blacklistsApi->findEntryByNumber(45, 12345678);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_delete_entry_by_id()
    {
        $api = Mockery::mock(InmobileApi::class);
        $blacklistsApi = new BlacklistApi($api);

        $api->shouldReceive('delete')
            ->with(
                '/blacklist/INBML-1',
            )
            ->andReturn(new Response('[]', 200))
            ->once();

        $response = $blacklistsApi->deleteEntryById('INBML-1');

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_delete_entry_by_number()
    {
        $api = Mockery::mock(InmobileApi::class);
        $blacklistsApi = new BlacklistApi($api);

        $api->shouldReceive('delete')
            ->with(
                '/blacklist/ByNumber',
                [
                    'countryCode' => '45',
                    'phoneNumber' => '12345678',
                ]
            )
            ->andReturn(new Response('[]', 200))
            ->once();

        $response = $blacklistsApi->deleteEntryByNumber(45, 12345678);

        $this->assertInstanceOf(Response::class, $response);
    }
}
