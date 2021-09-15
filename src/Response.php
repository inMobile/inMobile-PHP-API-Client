<?php

namespace Inmobile\InmobileSDK;

use stdClass;

class Response
{
    protected string $content;
    protected int $status;

    public function __construct(string $content, int $status)
    {
        $this->content = $content;
        $this->status = $status;
    }

    public function isOk(): bool
    {
        return $this->status >= 200 && $this->status < 300;
    }

    public function toArray(): ?array
    {
        return json_decode($this->content, true);
    }

    /**
     * @return array|stdClass|null
     */
    public function toObject()
    {
        return json_decode($this->content);
    }

    public function toString(): string
    {
        return $this->content;
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}
