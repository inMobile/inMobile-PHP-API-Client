<?php

namespace Inmobile\Tests\Unit\Endpoints;

use Inmobile\InmobileSDK\Endpoints\RecipientsApi;
use Inmobile\InmobileSDK\InmobileApi;
use Inmobile\InmobileSDK\RequestModels\Recipient;
use Inmobile\InmobileSDK\Response;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class RecipientsApiTest extends MockeryTestCase
{
    protected function validListResponse($next = null)
    {
        $lastPage = $next ? 'false' : 'true';

        return <<<JSON
        {
          "entries": [
            {
              "externalCreated": "2001-02-30T14:50:23Z",
              "numberInfo": {
                "countryCode": "45",
                "phoneNumber": "12345678"
              },
              "fields": {
                "firstname": "Friedhelm",
                "lastname": "Hillebrand",
                "birthday": "1945-04-30",
                "Custom1": "Custom1",
                "Custom2": "Custom2",
                "Custom3": "Custom3",
                "Custom4": "Custom4",
                "Custom5": "Custom5",
                "Custom6": "Custom6",
                "Email": "test@test.dk",
                "ZipCode": "8000",
                "Address": "testvej 12",
                "CompanyName": "test company"
              },
              "id": "string",
              "listId": "string",
              "created": "2001-02-30T14:50:23Z"
            }
          ],
          "_links": {
            "next": "$next",
            "isLastPage": $lastPage
          }
        }
        JSON;
    }

    protected function validRecipientResponse()
    {
        return <<<JSON
        {
          "externalCreated": "2001-02-30T14:50:23Z",
          "numberInfo": {
            "countryCode": "45",
            "phoneNumber": "12345678"
          },
          "fields": {
            "firstname": "Friedhelm",
            "lastname": "Hillebrand",
            "birthday": "1945-04-30",
            "Custom1": "Custom1",
            "Custom2": "Custom2",
            "Custom3": "Custom3",
            "Custom4": "Custom4",
            "Custom5": "Custom5",
            "Custom6": "Custom6",
            "Email": "test@test.dk",
            "ZipCode": "8000",
            "Address": "testvej 12",
            "CompanyName": "test company"
          },
          "id": "string",
          "listId": "string",
          "created": "2001-02-30T14:50:23Z"
        }
        JSON;
    }

    public function test_list_all_recipients()
    {
        $api = Mockery::mock(InmobileApi::class);
        $recipientsApi = new RecipientsApi($api);

        $api->shouldReceive('get')
            ->with(
                '/lists/foobar/recipients',
                ['pageLimit' => 250]
            )
            ->andReturn(new Response($this->validListResponse('/v4/lists/foobar/recipients/_nextpage'), 200))
            ->once();

        $api->shouldReceive('get')
            ->with(
                '/lists/foobar/recipients/_nextpage',
                ['pageLimit' => 250]
            )
            ->andReturn(new Response($this->validListResponse(), 200))
            ->once();

        $data = $recipientsApi->getAll('foobar');

        $this->assertIsArray($data);
        $this->assertCount(2, $data);
    }

    public function test_get_recipient_by_id()
    {
        $api = Mockery::mock(InmobileApi::class);
        $recipientsApi = new RecipientsApi($api);

        $api->shouldReceive('get')
            ->with(
                '/lists/foobar/recipients/REC-1',
            )
            ->andReturn(new Response($this->validRecipientResponse(), 200))
            ->once();

        $response = $recipientsApi->findById('foobar', 'REC-1');

        $this->assertInstanceOf(Recipient::class, $response);
    }

    public function test_get_recipient_by_phone_number()
    {
        $api = Mockery::mock(InmobileApi::class);
        $recipientsApi = new RecipientsApi($api);

        $api->shouldReceive('get')
            ->with(
                '/lists/foobar/recipients/ByNumber',
                [
                    'countryCode' => '45',
                    'phoneNumber' => '12345678',
                ]
            )
            ->andReturn(new Response($this->validRecipientResponse(), 200))
            ->once();

        $response = $recipientsApi->findByPhoneNumber('foobar', 45, 12345678);

        $this->assertInstanceOf(Recipient::class, $response);
    }

    public function test_create_recipient()
    {
        $api = Mockery::mock(InmobileApi::class);
        $recipientsApi = new RecipientsApi($api);
        $recipient = Recipient::create(45, 12345678)
            ->addField('firstname', 'John')
            ->addField('lastname', 'Doe');

        $api->shouldReceive('post')
            ->with(
                '/lists/foobar/recipients',
                $recipient->toArray(),
            )
            ->andReturn(new Response(new Response($this->validRecipientResponse(), 200))
            ->once();

        $response = $recipientsApi->create('foobar', $recipient);

        $this->assertInstanceOf(Recipient::class, $response);
    }

    public function test_update_recipient()
    {
        $api = Mockery::mock(InmobileApi::class);
        $recipientsApi = new RecipientsApi($api);
        $recipient = Recipient::create(45, 12345678)
            ->addField('firstname', 'John')
            ->addField('lastname', 'Doe');

        $api->shouldReceive('put')
            ->with(
                '/lists/foobar/recipients/REC-1',
                $recipient->toArray(),
            )
            ->andReturn(new Response('[]', 200))
            ->once();

        $response = $recipientsApi->update('foobar', 'REC-1', $recipient);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_delete_recipient()
    {
        $api = Mockery::mock(InmobileApi::class);
        $recipientsApi = new RecipientsApi($api);

        $api->shouldReceive('delete')
            ->with(
                '/lists/foobar/recipients/REC-1',
            )
            ->andReturn(new Response('[]', 200))
            ->once();

        $response = $recipientsApi->deleteById('foobar', 'REC-1');

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_delete_by_phone_number()
    {
        $api = Mockery::mock(InmobileApi::class);
        $recipientsApi = new RecipientsApi($api);

        $api->shouldReceive('delete')
            ->with(
                '/lists/foobar/recipients/ByNumber',
                [
                    'countryCode' => '45',
                    'phoneNumber' => '12345678',
                ]
            )
            ->andReturn(new Response('[]', 200))
            ->once();

        $response = $recipientsApi->deleteByPhoneNumber('foobar', 45, 12345678);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_delete_all_from_list()
    {
        $api = Mockery::mock(InmobileApi::class);
        $recipientsApi = new RecipientsApi($api);

        $api->shouldReceive('delete')
            ->with(
                '/lists/foobar/recipients/all',
            )
            ->andReturn(new Response('[]', 200))
            ->once();

        $response = $recipientsApi->deleteAllFromList('foobar');

        $this->assertInstanceOf(Response::class, $response);
    }
}
