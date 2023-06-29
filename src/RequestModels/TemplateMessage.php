<?php

namespace Inmobile\InmobileSDK\RequestModels;

use DateTime;
use DateTimeZone;

class TemplateMessage
{
    protected ?DateTime $sendTime = null;
    protected ?int $expireInSeconds = null;
    protected bool $respectBlacklist = true;
    protected ?string $statusCallbackUrl = null;
    protected ?string $messageId = null;
    protected ?string $countryHint = null;
    protected array $placeholders = [];

    /**
     * @var mixed
     */
    protected $recipient;

    public static function create(): self
    {
        return new self();
    }

    public function to($recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function setMessageId(?string $messageId): self
    {
        $this->messageId = $messageId;

        return $this;
    }

    public function sendAt(DateTime $sendTime): self
    {
        $this->sendTime = $sendTime;

        return $this;
    }

    public function expireIn(?int $seconds): self
    {
        $this->expireInSeconds = $seconds;

        return $this;
    }

    public function ignoreBlacklist(bool $shouldIgnore = false): self
    {
        $this->respectBlacklist = $shouldIgnore;

        return $this;
    }

    public function setStatusCallbackUrl(?string $url): self
    {
        $this->statusCallbackUrl = $url;

        return $this;
    }

    public function setCountryHint(string $countryHint): self
    {
        $this->countryHint = $countryHint;

        return $this;
    }

    public function setPlaceholders(array $placeholders): self
    {
        foreach ($placeholders as $key => $value) {
            if (strpos($key, '{') === 0 && strpos($key, '}') === strlen($key) - 1) {
                continue;
            }

            $placeholders[sprintf('{%s}', $key)] = $value;
            unset($placeholders[$key]);
        }

        $this->placeholders = $placeholders;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'to' => (string) $this->recipient,
            'countryHint' => $this->countryHint,
            'messageId' => $this->messageId,
            'sendTime' => $this->sendTime
                ? $this->sendTime->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z')
                : '',
            'validityPeriodInSeconds' => $this->expireInSeconds,
            'respectBlacklist' => $this->respectBlacklist,
            'statusCallbackUrl' => $this->statusCallbackUrl,
            'placeholders' => $this->placeholders,
        ];
    }

    public function getSendTime(): ?DateTime
    {
        return $this->sendTime;
    }

    public function getExpireInSeconds(): ?int
    {
        return $this->expireInSeconds;
    }

    public function getRecipient()
    {
        return $this->recipient;
    }

    public function getRespectBlacklist(): bool
    {
        return $this->respectBlacklist;
    }

    public function getStatusCallbackUrl(): ?string
    {
        return $this->statusCallbackUrl;
    }

    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    public function getCountryHint(): ?string
    {
        return $this->countryHint;
    }

    public function getPlaceholders(): array
    {
        return $this->placeholders;
    }
}
