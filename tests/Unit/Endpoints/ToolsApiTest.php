<?php

namespace Inmobile\Tests\Unit\Endpoints;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Inmobile\InmobileSDK\Endpoints\GDPRApi;
use Inmobile\InmobileSDK\Endpoints\ToolsApi;
use Inmobile\InmobileSDK\InmobileApi;
use Inmobile\InmobileSDK\RequestModels\NumberInfo;
use Inmobile\InmobileSDK\RequestModels\NumberToParse;
use Inmobile\InmobileSDK\RequestModels\Recipient;
use Inmobile\InmobileSDK\Response;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ToolsApiTest extends MockeryTestCase
{
    use ArraySubsetAsserts;

    protected function validResponse()
    {
        return <<<JSON
        {
            "countryCode": "45",
            "phoneNumber": "12345678",
            "rawMsisdn": "45 12 34 56 78",
            "msisdn": "4512345678",
            "isValidMsisdn": true,
            "countryHint": "DK"
        }
        JSON;
    }

    public function test_sends_a_parse_numbers_request()
    {
        $api = Mockery::mock(InmobileApi::class);
        $toolsApi = new ToolsApi($api);

        $numbers = [
            NumberToParse::create('DK', '12 34 56 78'),
            NumberToParse::create('45', '12 34 56 78'),
        ];

        $api->shouldReceive('post')
            ->with(
                '/tools/parsephonenumbers',
                Mockery::on(function ($payload) use ($numbers) {
                    $this->assertIsArray($payload);

                    $this->assertEquals(['numbersToParse' => array_map(fn (NumberToParse $numberToParse) => $numberToParse->toArray(), $numbers)], $payload);

                    return true;
                })
            )
            ->andReturn(new Response($this->validResponse(), 200))
            ->once();

        $toolsApi->numbersToParse($numbers);
    }

    public function test_sends_a_parse_number_request_with_a_single_instance_without_wrapping_it_in_an_array()
    {
        $api = Mockery::mock(InmobileApi::class);
        $toolsApi = new ToolsApi($api);

        $api->shouldReceive('post')
            ->with(
                '/tools/parsephonenumbers',
                Mockery::on(function ($payload) {
                    $this->assertEquals(['numbersToParse' => [NumberToParse::create('45', '12 34 56 78')->toArray()]], $payload);

                    return true;
                })
            )
            ->andReturn(new Response($this->validResponse(), 200))
            ->once();

        $toolsApi->numbersToParse(NumberToParse::create('45', '12 34 56 78'));
    }
}
