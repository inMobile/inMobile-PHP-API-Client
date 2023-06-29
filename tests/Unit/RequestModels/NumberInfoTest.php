<?php

namespace Inmobile\Tests\Unit\RequestModels;

use Inmobile\InmobileSDK\RequestModels\NumberInfo;
use PHPUnit\Framework\TestCase;

class NumberInfoTest extends TestCase
{
    public function test_can_convert_to_array()
    {
        $numberInfo = NumberInfo::create('45', '12345678');

        $this->assertEquals([
            'countryCode' => '45',
            'phoneNumber' => '12345678'
        ], $numberInfo->toArray());
    }
}
