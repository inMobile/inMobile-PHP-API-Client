<?php

namespace Inmobile\Tests\Unit\RequestModels;

use DateTime;
use Inmobile\InmobileSDK\RequestModels\TemplateMessage;
use PHPUnit\Framework\TestCase;

class TemplateMessageTest extends TestCase
{
    public function test_create_a_message()
    {
        $date = new DateTime('2021-01-02 03:04:05');
        $message = TemplateMessage::create()
            ->to(4512345678)
            ->setCountryHint('DK')
            ->setMessageId('SMS-1')
            ->expireIn(60)
            ->sendAt($date)
            ->ignoreBlacklist()
            ->setStatusCallbackUrl('https://example.com/callback')
            ->setPlaceholders(['name' => 'John', '{lastname}' => 'Doe']);

        $this->assertEquals(4512345678, $message->getRecipient());
        $this->assertEquals(60, $message->getExpireInSeconds());
        $this->assertEquals('SMS-1', $message->getMessageId());
        $this->assertEquals('DK', $message->getCountryHint());
        $this->assertEquals($date, $message->getSendTime());
        $this->assertFalse($message->getRespectBlacklist());
        $this->assertEquals('https://example.com/callback', $message->getStatusCallbackUrl());
        $this->assertEquals(['{name}' => 'John', '{lastname}' => 'Doe'], $message->getPlaceholders());
    }

    public function test_convert_to_array()
    {
        $date = new DateTime('2021-01-02 03:04:05');
        $message = TemplateMessage::create()
            ->to(4512345678)
            ->setCountryHint('DK')
            ->setMessageId('SMS-1')
            ->expireIn(60)
            ->sendAt($date)
            ->ignoreBlacklist()
            ->setStatusCallbackUrl('https://example.com/callback')
            ->setPlaceholders(['name' => 'John', 'lastname' => 'Doe']);

        $this->assertEquals([
            'to' => '4512345678',
            'countryHint' => 'DK',
            'messageId' => 'SMS-1',
            'sendTime' => '2021-01-02T03:04:05Z',
            'validityPeriodInSeconds' => 60,
            'respectBlacklist' => false,
            'statusCallbackUrl' => 'https://example.com/callback',
            'placeholders' => ['{name}' => 'John', '{lastname}' => 'Doe'],
        ], $message->toArray());
    }
}
