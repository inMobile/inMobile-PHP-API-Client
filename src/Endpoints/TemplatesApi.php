<?php

namespace Inmobile\InmobileSDK\Endpoints;

use Inmobile\InmobileSDK\InmobileApi;
use Inmobile\InmobileSDK\Response;
use Inmobile\InmobileSDK\Traits\HasPagination;

class TemplatesApi
{
    use HasPagination;

    protected InmobileApi $api;

    public function __construct(InmobileApi $api)
    {
        $this->api = $api;
    }

    public function getAll(): array
    {
        return $this->fetchAllFrom('/sms/templates');
    }

    public function find(string $id): Response
    {
        return $this->api->get('/sms/templates/' . $id);
    }
}
