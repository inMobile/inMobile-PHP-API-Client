<?php

namespace Inmobile\InmobileSDK\RequestModels;

class NumberInfo
{
    protected string $countryCode;
    protected string $phoneNumber;

    public function __construct(string $countryCode, string $phoneNumber)
    {
        $this->countryCode = $countryCode;
        $this->phoneNumber = $phoneNumber;
    }

    public static function create(string $countryCode, string $phoneNumber): self
    {
        return new self($countryCode, $phoneNumber);
    }

    public function toArray(): array
    {
        return [
            'countryCode' => $this->countryCode,
            'phoneNumber' => $this->phoneNumber,
        ];
    }
}