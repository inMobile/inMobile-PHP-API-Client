<?php

namespace Inmobile\InmobileSDK\Endpoints;

use Inmobile\InmobileSDK\InmobileApi;
use Inmobile\InmobileSDK\RequestModels\NumberInfo;
use Inmobile\InmobileSDK\Response;

class GDPRApi
{
    protected InmobileApi $api;

    public function __construct(InmobileApi $api)
    {
        $this->api = $api;
    }

    public function createDeletionRequest(NumberInfo $numberInfo): Response
    {
        return $this->api->post('/sms/gdpr/deletionrequests', ['numberInfo' => $numberInfo->toArray()]);
    }
}