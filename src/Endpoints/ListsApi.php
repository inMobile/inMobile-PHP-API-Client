<?php

namespace Inmobile\InmobileSDK\Endpoints;

use Inmobile\InmobileSDK\InmobileApi;
use Inmobile\InmobileSDK\Response;
use Inmobile\InmobileSDK\Traits\HasPagination;

class ListsApi
{
    use HasPagination;

    protected InmobileApi $api;

    public function __construct(InmobileApi $api)
    {
        $this->api = $api;
    }

    public function getAll(): array
    {
        return $this->fetchAllFrom('/lists');
    }

    public function find(string $id): Response
    {
        return $this->api->get('/lists/' . $id);
    }

    public function create(string $name): Response
    {
        return $this->api->post('/lists', ['name' => $name]);
    }

    public function update(string $id, string $name): Response
    {
        return $this->api->put('/lists/' . $id, ['name' => $name]);
    }

    public function delete(string $id): Response
    {
        return $this->api->delete('/lists/' . $id);
    }
}
