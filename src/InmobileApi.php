<?php

namespace Inmobile\InmobileSDK;

use Inmobile\InmobileSDK\Endpoints\BlacklistApi;
use Inmobile\InmobileSDK\Endpoints\ListsApi;
use Inmobile\InmobileSDK\Endpoints\MessagesApi;
use Inmobile\InmobileSDK\Endpoints\RecipientsApi;
use Inmobile\InmobileSDK\Exceptions\InmobileRequestFailedException;

class InmobileApi
{
    protected CurlClient $client;

    public function __construct(string $apiToken, ?CurlClient $client = null)
    {
        $this->client = $client ?: new CurlClient($apiToken, 'https://api.inmobile.com/v4');
    }

    public function messages(): MessagesApi
    {
        return new MessagesApi($this);
    }

    public function blacklist(): BlacklistApi
    {
        return new BlacklistApi($this);
    }

    public function lists(): ListsApi
    {
        return new ListsApi($this);
    }

    public function recipients(): RecipientsApi
    {
        return new RecipientsApi($this);
    }

    public function post($url, ?array $payload = null): Response
    {
        return $this->request('POST', $url, $payload);
    }

    public function put($url, ?array $payload = null): Response
    {
        return $this->request('PUT', $url, $payload);
    }

    public function delete($url, ?array $payload = null): Response
    {
        return $this->request('DELETE', $url, $payload);
    }

    public function get($url, ?array $payload = null): Response
    {
        return $this->request('GET', $url, $payload);
    }

    protected function request($method, $url, ?array $payload = null)
    {
        if ($method === 'GET') {
            if ($payload && count($payload) > 0) {
                $url .= '?' . http_build_query($payload);
            }

            $payload = null;
        }

        return $this->parseResponse(
            $this->client->request($method, $url, $payload)
        );
    }

    protected function parseResponse($responseData): Response
    {
        [$contents, $httpStatus] = $responseData;

        $response = new Response($contents, $httpStatus);

        if (!$response->isOk()) {
            throw InmobileRequestFailedException::fromResponse($response);
        }

        return $response;
    }
}
