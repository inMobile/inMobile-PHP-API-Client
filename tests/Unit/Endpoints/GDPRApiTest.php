<?php

namespace Inmobile\Tests\Unit\Endpoints;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Inmobile\InmobileSDK\Endpoints\GDPRApi;
use Inmobile\InmobileSDK\InmobileApi;
use Inmobile\InmobileSDK\RequestModels\NumberInfo;
use Inmobile\InmobileSDK\RequestModels\Recipient;
use Inmobile\InmobileSDK\Response;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class GDPRApiTest extends MockeryTestCase
{
    use ArraySubsetAsserts;

    protected function validResponse()
    {
        return <<<JSON
        {
          "id": "cf97f715-63d4-41df-92a1-34eb87b86gfd6b5"
        }
        JSON;
    }

    public function test_sends_a_deletion_request()
    {
        $api = Mockery::mock(InmobileApi::class);
        $gdprApi = new GDPRApi($api);

        $numberInfo = NumberInfo::create('45', '12345678');

        $api->shouldReceive('post')
            ->with(
                '/sms/gdpr/deletionrequests',
                Mockery::on(function ($payload) use ($numberInfo) {
                    $this->assertIsArray($payload);

                    $this->assertEquals(['numberInfo' => $numberInfo->toArray()], $payload);

                    return true;
                })
            )
            ->andReturn(new Response($this->validResponse(), 200))
            ->once();

        $gdprApi->createDeletionRequest($numberInfo);
    }
}
