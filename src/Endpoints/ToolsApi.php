<?php

namespace Inmobile\InmobileSDK\Endpoints;

use Inmobile\InmobileSDK\InmobileApi;
use Inmobile\InmobileSDK\RequestModels\NumberToParse;
use Inmobile\InmobileSDK\Response;

class ToolsApi
{
    protected InmobileApi $api;

    public function __construct(InmobileApi $api)
    {
        $this->api = $api;
    }

    /**
     * @param \Inmobile\InmobileSDK\RequestModels\NumberToParse[]|\Inmobile\InmobileSDK\RequestModels\NumberToParse $numbersToParse
     */
    public function numbersToParse($numbersToParse): Response
    {
        if (!is_array($numbersToParse)) {
            $numbersToParse = [$numbersToParse];
        }

        return $this->api->post(
            '/tools/parsephonenumbers',
            ['numbersToParse' => array_map(fn (NumberToParse $numberToParse) => $numberToParse->toArray(), $numbersToParse)]
        );
    }
}