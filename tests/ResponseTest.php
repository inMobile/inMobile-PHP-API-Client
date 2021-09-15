<?php

namespace Inmobile\Tests;

use Inmobile\InmobileSDK\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function test_can_convert_itself_to_an_array()
    {
        $response = new Response('{"result": [{"id": 1}, {"id": 2}]}', 200);

        $data = $response->toArray();

        $this->assertIsArray($data);
        $this->assertIsArray($data['result']);
        $this->assertCount(2, $data['result']);
        $this->assertEquals(1, $data['result'][0]['id']);
    }

    public function test_can_convert_itself_to_an_object()
    {
        $response = new Response('{"result": [{"id": 1}, {"id": 2}]}', 200);

        $data = $response->toObject();

        $this->assertIsObject($data);
        $this->assertIsArray($data->result);
        $this->assertCount(2, $data->result);
        $this->assertEquals(1, $data->result[0]->id);
    }

    public function test_can_convert_itself_to_a_string()
    {
        $content = '{"result": [{"id": 1}, {"id": 2}]}';
        $response = new Response($content, 200);

        $this->assertEquals($content, $response->toString());
    }

    public function test_can_get_the_status()
    {
        $response = new Response('{}', 200);

        $this->assertEquals(200, $response->getStatus());
    }

    /**
     * @test
     * @dataProvider statusCodeProvider
     */
    public function test_can_check_if_status_code_is_ok($statusCode, $expected)
    {
        $response = new Response('{}', $statusCode);

        $this->assertEquals(
            $expected,
            $response->isOk(),
            "Failed to assert that status code $statusCode was " . ($expected ? 'true' : 'false')
        );
    }

    public function statusCodeProvider()
    {
        return [
            [200, true],
            [400, false],
            [401, false],
            [500, false],
        ];
    }
}
