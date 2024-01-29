<?php

namespace Inmobile\Tests\Unit\Endpoints;

use Inmobile\InmobileSDK\Endpoints\EmailTemplatesApi;
use Inmobile\InmobileSDK\InmobileApi;
use Inmobile\InmobileSDK\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

class EmailTemplatesApiTest extends TestCase
{
    protected function validTemplateResponse(): string
    {
        return <<<JSON
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
        JSON;
    }

    protected function validTemplatesResponse(): string
    {
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
              "next": "/Feature/Something/cAw2WeS3rdf",
              "isLastPage": true
            }
        }
        JSON;
    }

    public function test_get_by_id()
    {
        $api = Mockery::mock(InmobileApi::class);
        $emailTemplatesApi = new EmailTemplatesApi($api);

        $api->shouldReceive('get')
            ->with('/email/templates/c7114ec3-8f89-4e26-8048-686585c1da2a')
            ->andReturn(new Response($this->validTemplateResponse(), 200))
            ->once();

        $response = $emailTemplatesApi->find('c7114ec3-8f89-4e26-8048-686585c1da2a');

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_get_all()
    {
        $api = Mockery::mock(InmobileApi::class);
        $emailTemplatesApi = new EmailTemplatesApi($api);

        $api->shouldReceive('get')
            ->with(
                '/email/templates',
                ['pageLimit' => 250]
            )
            ->andReturn(new Response($this->validTemplatesResponse(), 200))
            ->once();

        $response = $emailTemplatesApi->getAll();

        $this->assertIsArray($response);
    }
}
