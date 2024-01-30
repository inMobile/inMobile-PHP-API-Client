<?php

namespace Inmobile\Tests\Unit\Endpoints;

use Inmobile\InmobileSDK\Endpoints\EmailsApi;
use Inmobile\InmobileSDK\InmobileApi;
use Inmobile\InmobileSDK\RequestModels\Email;
use Inmobile\InmobileSDK\RequestModels\EmailRecipient;
use Inmobile\InmobileSDK\Response;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class EmailsApiTest extends MockeryTestCase
{
    protected function validEventsResponse(): string {
        return <<<JSON
        {
            "events": [
                {
                    "messageId": "id1",
                    "eventType": 3,
                    "eventTypeDescription": "Delivered",
                    "eventTimestamp": "2001-02-30T14:50:23Z"
                }
            ]
        }
        JSON;
    }

    public function test_get_events()
    {
        $api = Mockery::mock(InmobileApi::class);
        $emailsApi = new EmailsApi($api);

        $api->shouldReceive('get')
            ->with(
                '/email/outgoing/events',
                ['limit' => 250]
            )
            ->andReturn(new Response($this->validEventsResponse(), 200))
            ->once();

        $response = $emailsApi->getEvents(250);

        $this->assertIsArray($response->toArray()['events']);
    }

    protected function validEmailResponse(): string
    {
        return <<<JSON
        {
            "messageId": "8fe266b2-56e9-4b5f-938f-cc5e22530721",
            "to": [
                {
                  "emailAddress": "roy@tomlinson.com",
                  "displayName": "Roy Tomlinson"
                }
            ]
        }
        JSON;
    }

    public function test_send_email()
    {
        $api = Mockery::mock(InmobileApi::class);
        $emailsApi = new EmailsApi($api);

        $email = Email::create()
            ->subject('Hello World')
            ->from(EmailRecipient::create('john@example.com', 'John Doe'))
            ->to(EmailRecipient::create('jane@example.com', 'Jane Doe'))
            ->html('<h1>Hello World</h1>');

        $api->shouldReceive('post')
            ->with(
                '/email/outgoing',
                Mockery::on(function ($payload) use ($email) {
                    $this->assertIsArray($payload);

                    $this->assertEquals($email->toArray(), $payload);

                    return true;
                })
            )
            ->andReturn(new Response($this->validEmailResponse(), 200))
            ->once();

        $response = $emailsApi->send($email);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_send_email_using_template()
    {
        $api = Mockery::mock(InmobileApi::class);
        $emailsApi = new EmailsApi($api);

        $email = Email::create()
            ->subject('Hello World')
            ->from(EmailRecipient::create('john@example.com', 'John Doe'))
            ->to(EmailRecipient::create('jane@example.com', 'Jane Doe'))
            ->templateId('ecdcb257-c1e9-4b44-8a4e-f05822372d82')
            ->addPlaceholder('name', 'John Doe');

        $api->shouldReceive('post')
            ->with(
                '/email/outgoing/sendusingtemplate',
                Mockery::on(function ($payload) use ($email) {
                    $this->assertIsArray($payload);

                    $this->assertEquals($email->toArray(), $payload);

                    return true;
                })
            )
            ->andReturn(new Response($this->validEmailResponse(), 200))
            ->once();

        $response = $emailsApi->sendUsingTemplate($email);

        $this->assertInstanceOf(Response::class, $response);
    }
}
