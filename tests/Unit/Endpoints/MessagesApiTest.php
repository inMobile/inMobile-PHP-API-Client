<?php

namespace Inmobile\Tests\Unit\Endpoints;

use DateTime;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Inmobile\InmobileSDK\Endpoints\MessagesApi;
use Inmobile\InmobileSDK\InmobileApi;
use Inmobile\InmobileSDK\RequestModels\Message;
use Inmobile\InmobileSDK\RequestModels\TemplateMessage;
use Inmobile\InmobileSDK\Response;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class MessagesApiTest extends MockeryTestCase
{
    use ArraySubsetAsserts;

    protected function validResponse()
    {
        return <<<JSON
        {
          "results": [
            {
              "numberDetails": {
                "countryCode": "45",
                "phoneNumber": "12345678",
                "rawMsisdn": "45 12 34 56 78",
                "isValidMsisdn": true,
                "isAnonymized": false
              },
              "countryHint": "DK",
              "text": "Hello World!",
              "from": "INMBL",
              "smsCount": 1,
              "messageId": "SMS-1",
              "encoding": "gsm7"
            }
          ]
        }
        JSON;
    }

    public function test_sends_a_message()
    {
        $api = Mockery::mock(InmobileApi::class);
        $messagesApi = new MessagesApi($api);

        $sendAt = new DateTime('tomorrow');
        $message = Message::create('Hello World!')
            ->from('INMBL')
            ->to(12345678)
            ->setCountryHint('DK')
            ->sendAt($sendAt);

        $api->shouldReceive('post')
            ->with(
                '/sms/outgoing',
                Mockery::on(function ($payload) use ($message) {
                    $this->assertIsArray($payload);

                    $this->assertEquals($message->toArray(), $payload['messages'][0]);

                    return true;
                })
            )
            ->andReturn(new Response('[]', 200))
            ->once();

        $messagesApi->send($message);
    }

    public function test_sends_a_message_with_a_template_id()
    {
        $api = Mockery::mock(InmobileApi::class);
        $messagesApi = new MessagesApi($api);

        $sendAt = new DateTime('tomorrow');
        $message = TemplateMessage::create()
            ->to(12345678)
            ->setCountryHint('DK')
            ->setPlaceholders(['name' => 'John', 'lastname' => 'Doe'])
            ->sendAt($sendAt);

        $api->shouldReceive('post')
            ->with(
                '/sms/outgoing/sendusingtemplate',
                Mockery::on(function ($payload) use ($message) {
                    $this->assertIsArray($payload);

                    $this->assertEquals('ecdcb257-c1e9-4b44-8a4e-f05822372d82', $payload['templateId']);
                    $this->assertEquals($message->toArray(), $payload['messages'][0]);

                    return true;
                })
            )
            ->andReturn(new Response('[]', 200))
            ->once();

        $messagesApi->sendUsingTemplate($message, 'ecdcb257-c1e9-4b44-8a4e-f05822372d82');

    }

    public function test_sends_a_single_message_with_all_possible_fields()
    {
        $api = Mockery::mock(InmobileApi::class);
        $messagesApi = new MessagesApi($api);

        $sendAt = new DateTime('2021-08-28 12:34:54');
        $message = Message::create('Hello World!')
            ->from('INMBL')
            ->to(4512345678)
            ->setCountryHint('DK')
            ->expireIn(60)
            ->flash()
            ->ignoreBlacklist()
            ->setMessageId('system-5152')
            ->setEncoding(Message::ENCODING_UCS2)
            ->setStatusCallbackUrl('https://example.com/inmobile/callback')
            ->sendAt($sendAt);

        $api->shouldReceive('post')
            ->with(
                '/sms/outgoing',
                Mockery::on(function ($payload) use ($sendAt, $message) {
                    $this->assertIsArray($payload);

                    $this->assertEquals([
                        'to' => '4512345678',
                        'countryHint' => 'DK',
                        'text' => 'Hello World!',
                        'from' => 'INMBL',
                        'messageId' => 'system-5152',
                        'sendTime' => $sendAt->format('Y-m-d\TH:i:s\Z'),
                        'validityPeriodInSeconds' => 60,
                        'flash' => true,
                        'respectBlacklist' => false,
                        'statusCallbackUrl' => 'https://example.com/inmobile/callback',
                        'encoding' => Message::ENCODING_UCS2,
                    ], $payload['messages'][0]);

                    return true;
                })
            )
            ->andReturn(new Response('[]', 200))
            ->once();

        $messagesApi->send($message);
    }

    public function test_returns_the_response()
    {
        $api = Mockery::mock(InmobileApi::class);
        $messagesApi = new MessagesApi($api);

        $message = Message::create('Hello World!')
            ->from('INMBL')
            ->to(4512345678);

        $api->shouldReceive('post')
            ->with(
                '/sms/outgoing',
                Mockery::any(),
            )
            ->andReturn(new Response($this->validResponse(), 200))
            ->once();

        $response = $messagesApi->send($message);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_can_send_to_multiple_recipients()
    {
        $api = Mockery::mock(InmobileApi::class);
        $messagesApi = new MessagesApi($api);

        $message = Message::create('Hello World!')
            ->from('INMBL')
            ->to([4512345678, 4512345679]);

        $api->shouldReceive('post')
            ->with(
                '/sms/outgoing',
                Mockery::on(function ($payload) use ($message) {
                    $this->assertIsArray($payload);

                    $this->assertArraySubset([
                        'to' => '4512345678',
                        'text' => $message->getText(),
                        'from' => $message->getSender(),
                    ], $payload['messages'][0]);

                    $this->assertArraySubset([
                        'to' => '4512345679',
                        'text' => $message->getText(),
                        'from' => $message->getSender(),
                    ], $payload['messages'][1]);

                    return true;
                }),
            )
            ->andReturn(new Response($this->validResponse(), 200))
            ->once();

        $messagesApi->send($message);
    }

    public function test_get_status_reports()
    {
        $api = Mockery::mock(InmobileApi::class);
        $messagesApi = new MessagesApi($api);

        $api->shouldReceive('get')
            ->with(
                '/sms/outgoing/reports',
                ['limit' => 13]
            )
            ->andReturn(new Response('[]', 200))
            ->once();

        $response = $messagesApi->getStatusReport(13);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_cancel_messages()
    {
        $api = Mockery::mock(InmobileApi::class);
        $messagesApi = new MessagesApi($api);

        $api->shouldReceive('post')
            ->with(
                '/sms/outgoing/cancel',
                ['messageIds' => ['SMS-1', 'SMS-2', 'SMS-3']]
            )
            ->andReturn(new Response('[]', 200))
            ->once();

        $response = $messagesApi->cancel(['SMS-1', 'SMS-2', 'SMS-3']);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_cancel_single_message()
    {
        $api = Mockery::mock(InmobileApi::class);
        $messagesApi = new MessagesApi($api);

        $api->shouldReceive('post')
            ->with(
                '/sms/outgoing/cancel',
                ['messageIds' => ['SMS-1']]
            )
            ->andReturn(new Response('[]', 200))
            ->once();

        $response = $messagesApi->cancel('SMS-1');

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_send_single_message_using_query()
    {
        $api = Mockery::mock(InmobileApi::class);
        $messagesApi = new MessagesApi($api);

        $sendAt = new DateTime('tomorrow');
        $message = Message::create('Hello World!')
            ->from('INMBL')
            ->to(4512345678)
            ->setCountryHint('DK')
            ->sendAt($sendAt);

        $api->shouldReceive('get')
            ->with(
                '/sms/outgoing/sendusingquery',
                Mockery::on(function ($payload) use ($message) {
                    $this->assertIsArray($payload);

                    $expectedData = $message->toArray();
                    $expectedData['flash'] = 'false';
                    $expectedData['respectBlacklist'] = 'true';

                    $this->assertEquals($expectedData, $payload);

                    return true;
                })
            )
            ->andReturn(new Response('[]', 200))
            ->once();

        $response = $messagesApi->sendUsingQuery($message);

        $this->assertInstanceOf(Response::class, $response);
    }
}
