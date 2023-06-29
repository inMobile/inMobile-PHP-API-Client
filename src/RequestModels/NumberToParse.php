<?php

namespace Inmobile\InmobileSDK\RequestModels;

class NumberToParse
{
    protected string $countryHint;
    protected string $rawMsisdn;

    public function __construct(string $countryHint, string $rawMsisdn)
    {
        $this->countryHint = $countryHint;
        $this->rawMsisdn = $rawMsisdn;
    }

    public static function create(string $countryHint, string $rawMsisdn): self
    {
        return new self($countryHint, $rawMsisdn);
    }

    public function toArray(): array
    {
        return [
            'countryHint' => $this->countryHint,
            'rawMsisdn' => $this->rawMsisdn,
        ];
    }
}