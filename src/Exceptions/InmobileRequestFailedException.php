<?php

namespace Inmobile\InmobileSDK\Exceptions;

use Exception;
use Inmobile\InmobileSDK\Response;

class InmobileRequestFailedException extends Exception
{
    protected Response $response;

    public function __construct(Response $response, ?string $message = null)
    {
        parent::__construct(
            $message ?: ('The request failed with the following response: ' . $response->toObject()->errorMessage)
        );

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
