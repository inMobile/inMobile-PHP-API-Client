<?php

namespace Inmobile\InmobileSDK\Endpoints;

use Inmobile\InmobileSDK\InmobileApi;
use Inmobile\InmobileSDK\Response;
use Inmobile\InmobileSDK\Traits\HasPagination;

class BlacklistApi
{
    use HasPagination;

    protected InmobileApi $api;

    public function __construct(InmobileApi $api)
    {
        $this->api = $api;
    }

    public function get(int $limit = 20): Response
    {
        return $this->api->get('/blacklist', ['pageLimit' => $limit]);
    }

    public function getAll(): array
    {
        return $this->fetchAllFrom('/blacklist');
    }

    public function findEntryById(string $id): Response
    {
        return $this->api->get('/blacklist/' . $id);
    }

    /**
     * @param string|int $countryCode
     * @param string|int $phoneNumber
     *
     * @return \Inmobile\InmobileSDK\Response
     */
    public function findEntryByNumber($countryCode, $phoneNumber): Response
    {
        return $this->api->get('/blacklist/ByNumber', [
            'countryCode' => (string) $countryCode,
            'phoneNumber' => (string) $phoneNumber,
        ]);
    }

    /**
     * @param string|int  $countryCode
     * @param string|int  $phoneNumber
     * @param string|null $comment
     *
     * @return \Inmobile\InmobileSDK\Response
     */
    public function createEntry($countryCode, $phoneNumber, ?string $comment = null): Response
    {
        $blacklistPayload = [
            'numberInfo' => [
                'countryCode' => (string) $countryCode,
                'phoneNumber' => (string) $phoneNumber,
            ],
            'comment' => $comment
        ];

        return $this->api->post('/blacklist', $blacklistPayload);
    }

    public function deleteEntryById(string $id): Response
    {
        return $this->api->delete('/blacklist/' . $id);
    }

    /**
     * @param string|int $countryCode
     * @param string|int $phoneNumber
     *
     * @return \Inmobile\InmobileSDK\Response
     */
    public function deleteEntryByNumber($countryCode, $phoneNumber): Response
    {
        return $this->api->delete('/blacklist/ByNumber', [
            'countryCode' => (string) $countryCode,
            'phoneNumber' => (string) $phoneNumber,
        ]);
    }
}
