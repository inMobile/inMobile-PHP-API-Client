<?php

namespace Inmobile\InmobileSDK\RequestModels;

use DateTime;
use DateTimeZone;

class Message
{
    public const ENCODING_AUTO = 'auto';
    public const ENCODING_GSM7 = 'gsm7';
    public const ENCODING_UCS2 = 'ucs2';

    protected string $text;
    protected string $sender;
    protected ?DateTime $sendTime = null;
    protected ?int $expireInSeconds = null;
    protected bool $flash = false;
    protected bool $respectBlacklist = true;
    protected ?string $statusCallbackUrl = null;
    protected string $encoding = self::ENCODING_GSM7;
    protected ?string $messageId = null;
    protected ?string $countryHint = null;
    protected ?int $msisdnCooldownInMinutes = null;

    /**
     * @var mixed
     */
    protected $recipient;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public static function create(string $contents): self
    {
        return new self($contents);
    }

    public function from(string $sender): self
    {
        $this->sender = $sender;

        return $this;
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

    public function flash(bool $shouldFlash = true): self
    {
        $this->flash = $shouldFlash;

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

    public function setEncoding(string $encoding): self
    {
        $this->encoding = $encoding;

        return $this;
    }

    public function setCountryHint(string $countryHint): self
    {
        $this->countryHint = $countryHint;

        return $this;
    }

    public function setMsisdnCooldownInMinutes(?int $msisdnCooldownInMinutes): self
    {
        $this->msisdnCooldownInMinutes = $msisdnCooldownInMinutes;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'to' => (string) $this->recipient,
            'countryHint' => $this->countryHint,
            'text' => $this->text,
            'from' => $this->sender,
            'messageId' => $this->messageId,
            'sendTime' => $this->sendTime
                ? $this->sendTime->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z')
                : '',
            'validityPeriodInSeconds' => $this->expireInSeconds,
            'flash' => $this->flash,
            'respectBlacklist' => $this->respectBlacklist,
            'statusCallbackUrl' => $this->statusCallbackUrl,
            'encoding' => $this->encoding,
            'msisdnCooldownInMinutes' => $this->msisdnCooldownInMinutes,
        ];
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getSender(): string
    {
        return $this->sender;
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

    public function getFlash(): bool
    {
        return $this->flash;
    }

    public function getRespectBlacklist(): bool
    {
        return $this->respectBlacklist;
    }

    public function getStatusCallbackUrl(): ?string
    {
        return $this->statusCallbackUrl;
    }

    public function getEncoding(): string
    {
        return $this->encoding;
    }

    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    public function getCountryHint(): ?string
    {
        return $this->countryHint;
    }

    public function getMsisdnCooldownInMinutes(): ?int
    {
        return $this->msisdnCooldownInMinutes;
    }
}
