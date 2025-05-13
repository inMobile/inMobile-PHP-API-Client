<?php

namespace Inmobile\Tests\Unit\RequestModels;

use DateTime;
use Inmobile\InmobileSDK\RequestModels\Email;
use Inmobile\InmobileSDK\RequestModels\EmailRecipient;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function test_can_convert_to_array()
    {
        $email = Email::create()
            ->from(EmailRecipient::create('john@example.com', 'John Doe'))
            ->to(EmailRecipient::create('jane@example.com', 'Jane Doe'))
            ->replyTo(EmailRecipient::create('bob@example.com', 'Bob Doe'))
            ->subject('Hello World')
            ->html('<h1>Hello World</h1>')
            ->text('Hello World')
            ->templateId('ecdcb257-c1e9-4b44-8a4e-f05822372d82')
            ->messageId('8fe266b2-56e9-4b5f-938f-cc5e22530721')
            ->sendAt(new DateTime('2021-01-02 03:04:05'))
            ->tracking(true)
            ->listUnsubscribe(false)
            ->addPlaceholder('name', 'John Doe');

        $this->assertEquals(
            [
                'from' => [
                    'emailAddress' => 'john@example.com',
                    'displayName' => 'John Doe',
                ],
                'to' => [
                    [
                        'emailAddress' => 'jane@example.com',
                        'displayName' => 'Jane Doe',
                    ],
                ],
                'replyTo' => [
                    [
                        'emailAddress' => 'bob@example.com',
                        'displayName' => 'Bob Doe',
                    ],
                ],
                'subject' => 'Hello World',
                'html' => '<h1>Hello World</h1>',
                'text' => 'Hello World',
                'templateId' => 'ecdcb257-c1e9-4b44-8a4e-f05822372d82',
                'messageId' => '8fe266b2-56e9-4b5f-938f-cc5e22530721',
                'sendTime' => '2021-01-02T03:04:05Z',
                'tracking' => true,
                'listUnsubscribe' => false,
                'placeholders' => [
                    '{name}' => 'John Doe',
                ],
            ],
            $email->toArray()
        );
    }

    public function test_ignores_unset_optional_fields()
    {
        $email = Email::create()
            ->from(EmailRecipient::create('john@example.com', 'John Doe'))
            ->to(EmailRecipient::create('jane@example.com', 'Jane Doe'));

        $this->assertEquals(
            [
                'from' => [
                    'emailAddress' => 'john@example.com',
                    'displayName' => 'John Doe',
                ],
                'to' => [
                    [
                        'emailAddress' => 'jane@example.com',
                        'displayName' => 'Jane Doe',
                    ],
                ],
            ],
            $email->toArray()
        );
    }
}
