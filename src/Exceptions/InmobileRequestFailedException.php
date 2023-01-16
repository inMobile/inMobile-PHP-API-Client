<?php

namespace Inmobile\InmobileSDK\Exceptions;

use Exception;
use Inmobile\InmobileSDK\Response;

class InmobileRequestFailedException extends Exception
{
    protected Response $response;

    public function __construct(Response $response, ?string $message = null)
    {
        if ($response->toObject() && property_exists($response->toObject(), 'errorMessage')) {
            $message = 'The request failed with the following response: ' . $response->toObject()->errorMessage;
        } elseif (!$message) {
            $message = 'The request failed with an empty response and status code: ' . $response->getStatus();
        }

        parent::__construct($message);

        $this->response = $response;
    }

    public static function fromResponse(Response $response): self
    {
        return new self($response);
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
