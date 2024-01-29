<?php

namespace Inmobile\InmobileSDK\Exceptions;

use Exception;
use Inmobile\InmobileSDK\Response;

class InmobileRequestFailedException extends Exception
{
    protected Response $response;

    /** @var string[] */
    protected array $details = [];

    public function __construct(Response $response, ?string $message = null)
    {
        $responseObject = $response->toObject();

        if ($responseObject && property_exists($responseObject, 'errorMessage')) {
            $message = 'The request failed with the following response: ' . $responseObject->errorMessage;

            if (property_exists($responseObject, 'details') && is_array($responseObject->details)) {
                $this->details = $responseObject->details;
            }
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

    /**
     * @return string[]
     */
    public function getDetails(): array
    {
        return $this->details;
    }
}
