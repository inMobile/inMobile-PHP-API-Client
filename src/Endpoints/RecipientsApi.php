<?php

namespace Inmobile\InmobileSDK\Endpoints;

use DateTime;
use Inmobile\InmobileSDK\InmobileApi;
use Inmobile\InmobileSDK\RequestModels\Recipient;
use Inmobile\InmobileSDK\Response;
use Inmobile\InmobileSDK\Traits\HasPagination;
use stdClass;

class RecipientsApi
{
    use HasPagination;

    protected InmobileApi $api;

    public function __construct(InmobileApi $api)
    {
        $this->api = $api;
    }

    public function getAll(string $listId): array
    {
        return $this->fetchAllFrom('/lists/' . $listId . '/recipients');
    }

    public function findById(string $listId, string $id): Recipient
    {
        $response = $this->api->get('/lists/' . $listId . '/recipients/' . $id);

        return $this->convertToRecipient($response->toObject());
    }

    public function findByPhoneNumber(string $listId, $countryCode, $phoneNumber): Recipient
    {
        $response = $this->api->get('/lists/' . $listId . '/recipients/ByNumber', [
            'countryCode' => (string)$countryCode,
            'phoneNumber' => (string)$phoneNumber,
        ]);

        return $this->convertToRecipient($response->toObject());
    }

    public function create(string $listId, Recipient $recipient): Recipient
    {
        $response = $this->api->post('/lists/' . $listId . '/recipients', $recipient->toArray());

        return $this->convertToRecipient($response->toObject());
    }

    public function update(string $listId, string $id, Recipient $recipient): Response
    {
        return $this->api->put('/lists/' . $listId . '/recipients/' . $id, $recipient->toArray());
    }

    /**
     * Create or update recipient.
     *
     * @param string     $listId
     * @param string|int $countryCode   Phone country code of the recipient to search.
     * @param string|int $phoneNumber   Phone number of the recipient to search.
     * @param Recipient  $recipient      Recipient data to update or create.
     * 
     * @return Recipient
     */
    public function createOrUpdateByPhoneNumber(string $listId, $countryCode, $phoneNumber, Recipient $recipient): Recipient
    {
        $qry = http_build_query([
            'countryCode' => (string)$countryCode,
            'phoneNumber' => (string)$phoneNumber
        ]);

        $response = $this->api->post('/lists/' . $listId . '/recipients/byNumber?' . $qry, $recipient->toArray());

        return $this->convertToRecipient($response->toObject());
    }

    public function deleteById(string $listId, string $id): Response
    {
        return $this->api->delete('/lists/' . $listId . '/recipients/' . $id);
    }

    /**
     * @param string     $listId
     * @param string|int $countryCode
     * @param string|int $phoneNumber
     *
     * @return \Inmobile\InmobileSDK\Response
     */
    public function deleteByPhoneNumber(string $listId, $countryCode, $phoneNumber): Response
    {
        return $this->api->delete('/lists/' . $listId . '/recipients/ByNumber', [
            'countryCode' => (string)$countryCode,
            'phoneNumber' => (string)$phoneNumber,
        ]);
    }

    public function deleteAllFromList(string $listId): Response
    {
        return $this->api->delete('/lists/' . $listId . '/recipients/all');
    }

    protected function convertToRecipient(stdClass $response): Recipient
    {
        $recipient = Recipient::create($response->numberInfo->countryCode, $response->numberInfo->phoneNumber)
            ->createdAt(new DateTime($response->created))
            ->setFields((array)$response->fields)
            ->setId($response->id)
            ->setListId($response->listId);

        return $recipient;
    }
}
