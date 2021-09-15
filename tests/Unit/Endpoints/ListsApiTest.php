<?php

namespace Inmobile\Tests\Unit\Endpoints;

use Inmobile\InmobileSDK\Endpoints\ListsApi;
use Inmobile\InmobileSDK\InmobileApi;
use Inmobile\InmobileSDK\Response;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ListsApiTest extends MockeryTestCase
{
    protected function validListResponse($next = null)
    {
        $lastPage = $next ? 'false' : 'true';

        return <<<JSON
        {
          "entries": [
            {
              "id": "string",
              "name": "string",
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

    public function test_get_list()
    {
        $api = Mockery::mock(InmobileApi::class);
        $listsApi = new ListsApi($api);

        $api->shouldReceive('get')
            ->with('/lists/foobar')
            ->andReturn(new Response('[]', 200))
            ->once();

        $response = $listsApi->find('foobar');

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_create_list()
    {
        $api = Mockery::mock(InmobileApi::class);
        $listsApi = new ListsApi($api);

        $api->shouldReceive('post')
            ->with(
                '/lists',
                ['name' => 'foobar']
            )
            ->andReturn(new Response('[]', 200))
            ->once();

        $response = $listsApi->create('foobar');

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_delete_list()
    {
        $api = Mockery::mock(InmobileApi::class);
        $listsApi = new ListsApi($api);

        $api->shouldReceive('delete')
            ->with(
                '/lists/foobar',
            )
            ->andReturn(new Response('[]', 200))
            ->once();

        $response = $listsApi->delete('foobar');

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_update_list()
    {
        $api = Mockery::mock(InmobileApi::class);
        $listsApi = new ListsApi($api);

        $api->shouldReceive('put')
            ->with(
                '/lists/foobar',
                ['name' => 'barbiz']
            )
            ->andReturn(new Response('[]', 200))
            ->once();

        $response = $listsApi->update('foobar', 'barbiz');

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_get_lists()
    {
        $api = Mockery::mock(InmobileApi::class);
        $listsApi = new ListsApi($api);

        $api->shouldReceive('get')
            ->with(
                '/lists',
                ['pageLimit' => 13],
            )
            ->andReturn(new Response('[]', 200))
            ->once();

        $response = $listsApi->get(13);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_get_all_from_lists()
    {
        $api = Mockery::mock(InmobileApi::class);
        $listsApi = new ListsApi($api);

        $api->shouldReceive('get')
            ->with(
                '/lists',
                ['pageLimit' => 250]
            )
            ->andReturn(new Response($this->validListResponse('/v4/lists/foobar'), 200))
            ->once();

        $api->shouldReceive('get')
            ->with(
                '/lists/foobar',
                ['pageLimit' => 250]
            )
            ->andReturn(new Response($this->validListResponse(), 200))
            ->once();

        $data = $listsApi->getAll();

        $this->assertIsArray($data);
        $this->assertCount(2, $data);
    }
}
