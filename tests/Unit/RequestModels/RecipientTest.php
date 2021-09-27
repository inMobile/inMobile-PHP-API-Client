<?php

namespace Inmobile\Tests\Unit\RequestModels;

use DateTime;
use Inmobile\InmobileSDK\RequestModels\Recipient;
use PHPUnit\Framework\TestCase;

class RecipientTest extends TestCase
{
    public function test_can_create_a_recipient()
    {
        $date = new DateTime('now');
        $recipient = Recipient::create(45, 12345678)
            ->addField('firstname', 'John')
            ->addField('lastname', 'Doe')
            ->createdAt($date);

        $this->assertEquals(45, $recipient->getCountryCode());
        $this->assertEquals(12345678, $recipient->getPhoneNumber());
        $this->assertEquals([
            'firstname' => 'John',
            'lastname' => 'Doe',
        ], $recipient->getFields());
        $this->assertEquals($date, $recipient->getCreatedAt());
    }

    public function test_can_convert_to_array()
    {
        $date = new DateTime('2021-01-02 03:04:05');
        $recipient = Recipient::create(45, 12345678)
            ->addField('firstname', 'John')
            ->addField('lastname', 'Doe')
            ->createdAt($date);

        $this->assertEquals([
            'numberInfo' => [
                'countryCode' => '45',
                'phoneNumber' => '12345678'
            ],
            'fields' => (object) [
                'firstname' => 'John',
                'lastname' => 'Doe',
            ],
            'externalCreated' => '2021-01-02T03:04:05Z'
        ], $recipient->toArray());
    }

    public function test_converts_empty_fields_array_to_object()
    {
        $recipient = Recipient::create(45, 12345678);

        $this->assertEquals([
            'numberInfo' => [
                'countryCode' => '45',
                'phoneNumber' => '12345678'
            ],
            'fields' => new \stdClass(),
        ], $recipient->toArray());
    }
}
