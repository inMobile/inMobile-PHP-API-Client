<?php

namespace Inmobile\Tests\Unit\RequestModels;

use Inmobile\InmobileSDK\RequestModels\NumberToParse;
use PHPUnit\Framework\TestCase;

class NumberToParseTest extends TestCase
{
    public function test_can_convert_to_array()
    {
        $numbersPayload = [
            NumberToParse::create('DK', '45 12 34 56 78'),
            NumberToParse::create('45', '45 12 34 56 78')
        ];

        $this->assertEquals([
            [
                'countryHint' => 'DK',
                'rawMsisdn' => '45 12 34 56 78',
            ],
            [
                'countryHint' => '45',
                'rawMsisdn' => '45 12 34 56 78',
            ]
        ], array_map(fn (NumberToParse $numberToParse) => $numberToParse->toArray(), $numbersPayload));
    }
}
