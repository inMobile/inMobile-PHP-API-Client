<?php

namespace Inmobile\Tests\Unit\RequestModels;

use DateTime;
use Inmobile\InmobileSDK\RequestModels\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function test_create_a_message()
    {
        $date = new DateTime('2021-01-02 03:04:05');
        $message = Message::create('Hello World')
            ->to(4512345678)
            ->from('INMBL')
            ->setMessageId('SMS-1')
            ->expireIn(60)
            ->sendAt($date)
            ->flash()
            ->ignoreBlacklist()
            ->setEncoding(Message::ENCODING_GSM7)
            ->setStatusCallbackUrl('https://example.com/callback');

        $this->assertEquals('Hello World', $message->getText());
        $this->assertEquals(4512345678, $message->getRecipient());
        $this->assertEquals(60, $message->getExpireInSeconds());
        $this->assertEquals('INMBL', $message->getSender());
        $this->assertEquals('SMS-1', $message->getMessageId());
        $this->assertEquals($date, $message->getSendTime());
        $this->assertTrue($message->getFlash());
        $this->assertFalse($message->getRespectBlacklist());
        $this->assertEquals(Message::ENCODING_GSM7, $message->getEncoding());
        $this->assertEquals('https://example.com/callback', $message->getStatusCallbackUrl());
    }

    public function test_convert_to_array()
    {
        $date = new DateTime('2021-01-02 03:04:05');
        $message = Message::create('Hello World')
            ->to(4512345678)
            ->from('INMBL')
            ->setMessageId('SMS-1')
            ->expireIn(60)
            ->sendAt($date)
            ->flash()
            ->ignoreBlacklist()
            ->setEncoding(Message::ENCODING_GSM7)
            ->setStatusCallbackUrl('https://example.com/callback');

        $this->assertEquals([
            'to' => '4512345678',
            'text' => 'Hello World',
            'from' => 'INMBL',
            'messageId' => 'SMS-1',
            'sendTime' => '2021-01-02T03:04:05Z',
            'validityPeriodInSeconds' => 60,
            'flash' => true,
            'respectBlacklist' => false,
            'statusCallbackUrl' => 'https://example.com/callback',
            'encoding' => Message::ENCODING_GSM7,
        ], $message->toArray());
    }
}
