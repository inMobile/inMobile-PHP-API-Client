<?php

namespace Inmobile\InmobileSDK\RequestModels;

use DateTime;
use DateTimeZone;

class Recipient
{
    /**
     * @var string|int
     */
    protected $countryCode;

    /**
     * @var string|int
     */
    protected $phoneNumber;

    protected array $fields = [];
    protected ?DateTime $createdAt;


    /**
     * @param string|int $countryCode
     * @param string|int $phoneNumber
     */
    public function __construct($countryCode, $phoneNumber)
    {
        $this->countryCode = $countryCode;
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @param string|int $countryCode
     * @param string|int $phoneNumber
     *
     * @return static
     */
    public static function create($countryCode, $phoneNumber): self
    {
        return new self($countryCode, $phoneNumber);
    }

    public function createdAt(DateTime $dateTime): Recipient
    {
        $this->createdAt = $dateTime;

        return $this;
    }

    public function addField(string $key, string $value): self
    {
        $this->fields[$key] = $value;

        return $this;
    }

    public function toArray(): array
    {
        $data = [
            'numberInfo' => [
                'countryCode' => (string) $this->countryCode,
                'phoneNumber' => (string) $this->phoneNumber,
            ],
            'fields' => (object) $this->fields,
        ];

        if (isset($this->createdAt)) {
            $data['externalCreated'] = $this->createdAt
                ? $this->createdAt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z')
                : null;
        }

        return $data;
    }

    /**
     * @return int|string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @return int|string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): Recipient
    {
        $this->fields = $fields;

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }
}
