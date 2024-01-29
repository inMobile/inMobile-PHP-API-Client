<?php

namespace Inmobile\InmobileSDK\RequestModels;

class EmailRecipient
{
    protected string $email;
    protected string $name;

    public function __construct(string $email, string $name)
    {
        $this->email = $email;
        $this->name = $name;
    }

    public static function create(string $email, string $name): self
    {
        return new self($email, $name);
    }

    public function toArray(): array
    {
        return [
            'emailAddress' => $this->email,
            'displayName' => $this->name,
        ];
    }
}
