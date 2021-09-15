<?php

namespace Inmobile\InmobileSDK\Endpoints;

use Inmobile\InmobileSDK\InmobileApi;
use Inmobile\InmobileSDK\RequestModels\Recipient;
use Inmobile\InmobileSDK\Response;
use Inmobile\InmobileSDK\Traits\HasPagination;

class RecipientsApi
{
    use HasPagination;

    protected InmobileApi $api;

    public function __construct(InmobileApi $api)
    {
        $this->api = $api;
    }

    public function get(string $listId, int $limit = 20): Response
    {
        return $this->api->get('/lists/' . $listId . '/recipients', ['pageLimit' => $limit]);
    }

    public function getAll(string $listId): array
    {
        return $this->fetchAllFrom('/lists/' . $listId . '/recipients');
    }

    public function findById(string $listId, string $id): Response
    {
        return $this->api->get('/lists/' . $listId . '/recipients/' . $id);
    }

    /**
     * @param string     $listId
     * @param string|int $countryCode
     * @param string|int $phoneNumber
     *
     * @return \Inmobile\InmobileSDK\Response
     */
    public function findByPhoneNumber(string $listId, $countryCode, $phoneNumber): Response
    {
        return $this->api->get('/lists/' . $listId . '/recipients/ByNumber', [
            'countryCode' => (string) $countryCode,
            'phoneNumber' => (string) $phoneNumber,
        ]);
    }

    public function create(string $listId, Recipient $recipient): Response
    {
        return $this->api->post('/lists/' . $listId . '/recipients', $recipient->toArray());
    }

    public function update(string $listId, string $id, Recipient $recipient): Response
    {
        return $this->api->put('/lists/' . $listId . '/recipients/' . $id, $recipient->toArray());
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
            'countryCode' => (string) $countryCode,
            'phoneNumber' => (string) $phoneNumber,
        ]);
    }

    public function deleteAllFromList(string $listId): Response
    {
        return $this->api->delete('/lists/' . $listId . '/recipients/all');
    }
}
