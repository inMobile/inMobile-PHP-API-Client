<?php

namespace Inmobile\Tests\Unit\RequestModels;

use Inmobile\InmobileSDK\RequestModels\EmailRecipient;
use PHPUnit\Framework\TestCase;

class EmailRecipientTest extends TestCase
{
    public function test_can_convert_to_array()
    {
        $recipient = EmailRecipient::create('john@example.com', 'John Doe');

        $this->assertEquals(
            [
                'emailAddress' => 'john@example.com',
                'displayName' => 'John Doe',
            ],
            $recipient->toArray()
        );
    }
}
