<?php

namespace Inmobile\Tests\Unit\Endpoints;

use Inmobile\InmobileSDK\Endpoints\ListsApi;
use Inmobile\InmobileSDK\Endpoints\TemplatesApi;
use Inmobile\InmobileSDK\InmobileApi;
use Inmobile\InmobileSDK\Response;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class TemplatesApiTest extends MockeryTestCase
{
    protected function validListResponse($next = null)
    {
        $lastPage = $next ? 'false' : 'true';

        return <<<JSON
        {
            "entries": [
                {
                    "id": "c7114ec3-8f89-4e26-8048-686585c1da2a",
                    "name": "My template",
                    "text": "My template text {name} {lastname}",
                    "senderName": "My sendername",
                    "encoding": "gsm7",
                    "placeholders": [
                        "{name}",
                        "{lastname}"
                    ],
                    "created": "2001-02-30T14:50:23Z",
                    "lastUpdated": "2001-02-30T14:50:23Z"
                  }
            ],
            "_links": {
                "next": "{$next}",
                "isLastPage": {$lastPage}
            }
        }
        JSON;
    }

    public function test_get_template()
    {
        $api = Mockery::mock(InmobileApi::class);
        $templatesApi = new TemplatesApi($api);

        $api->shouldReceive('get')
            ->with('/sms/templates/foobar')
            ->andReturn(new Response('[]', 200))
            ->once();

        $response = $templatesApi->find('foobar');

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_get_all_from_templates()
    {
        $api = Mockery::mock(InmobileApi::class);
        $templatesApi = new TemplatesApi($api);

        $api->shouldReceive('get')
            ->with(
                '/sms/templates',
                ['pageLimit' => 250]
            )
            ->andReturn(new Response($this->validListResponse('/v4/sms/templates/foobar'), 200))
            ->once();

        $api->shouldReceive('get')
            ->with(
                '/sms/templates/foobar',
                ['pageLimit' => 250]
            )
            ->andReturn(new Response($this->validListResponse(), 200))
            ->once();

        $data = $templatesApi->getAll();

        $this->assertIsArray($data);
        $this->assertCount(2, $data);
    }
}
