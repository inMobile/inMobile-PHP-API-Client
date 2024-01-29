<?php

namespace Inmobile\InmobileSDK\Endpoints;

use Inmobile\InmobileSDK\InmobileApi;
use Inmobile\InmobileSDK\Response;
use Inmobile\InmobileSDK\Traits\HasPagination;

class EmailTemplatesApi
{
    use HasPagination;

    protected InmobileApi $api;

    public function __construct(InmobileApi $api)
    {
        $this->api = $api;
    }

    public function getAll(): array
    {
        return $this->fetchAllFrom('/email/templates');
    }

    public function find(string $id): Response
    {
        return $this->api->get('/email/templates/' . $id);
    }
}
