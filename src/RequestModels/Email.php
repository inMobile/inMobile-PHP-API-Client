<?php

namespace Inmobile\InmobileSDK\RequestModels;

use DateTime;

class Email
{
    protected EmailRecipient $sender;

    /** @var EmailRecipient[] */
    protected array $recipients;

    /** @var EmailRecipient[] */
    protected array $replyTo = [];

    protected ?string $subject = null;

    protected ?string $html = null;

    protected ?string $text = null;

    protected ?string $templateId = null;

    protected ?string $messageId = null;

    protected ?DateTime $sendTime = null;

    protected ?bool $tracking = null;
    
    protected ?bool $listUnsubscribe = null;

    /** @var array<string, string> */
    protected ?array $placeholders = [];

    public static function create(): self
    {
        return new self();
    }

    public function from(EmailRecipient $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function to(EmailRecipient ...$recipient): self
    {
        $this->recipients = $recipient;

        return $this;
    }

    public function replyTo(EmailRecipient ...$replyTo): self
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    public function subject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function html(?string $html): self
    {
        $this->html = $html;

        return $this;
    }

    public function text(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function templateId(?string $templateId): self
    {
        $this->templateId = $templateId;

        return $this;
    }

    public function messageId(?string $messageId): self
    {
        $this->messageId = $messageId;

        return $this;
    }

    public function sendAt(DateTime $sendTime): self
    {
        $this->sendTime = $sendTime;

        return $this;
    }

    public function tracking(bool $tracking): self
    {
        $this->tracking = $tracking;

        return $this;
    }

    public function listUnsubscribe(bool $listUnsubscribe): self
    {
        $this->listUnsubscribe = $listUnsubscribe;

        return $this;
    }

    public function addPlaceholder(string $key, string $value): self
    {
        if ($key[0] !== '{' || $key[strlen($key) - 1] !== '}') {
            $key = sprintf('{%s}', $key);
        }

        $this->placeholders[$key] = $value;

        return $this;
    }

    /**
     * @param array<string, string> $placeholders
     */
    public function placeholders(?array $placeholders): self
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

    public function getSender(): EmailRecipient
    {
        return $this->sender;
    }

    public function getRecipients(): array
    {
        return $this->recipients;
    }

    public function getTemplateId(): ?string
    {
        return $this->templateId;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function getPlaceholders(): ?array
    {
        return $this->placeholders;
    }

    public function toArray(): array
    {
        $array = [
            'from' => $this->sender->toArray(),
            'to' => array_map(fn ($recipient) => $recipient->toArray(), $this->recipients),
            'replyTo' => array_map(fn ($replyTo) => $replyTo->toArray(), $this->replyTo),
            'subject' => $this->subject,
            'html' => $this->html,
            'text' => $this->text,
            'templateId' => $this->templateId,
            'messageId' => $this->messageId,
            'sendTime' => $this->sendTime ? $this->sendTime->format('Y-m-d\TH:i:s\Z') : null,
            'tracking' => $this->tracking,
            'listUnsubscribe' => $this->listUnsubscribe,
            'placeholders' => $this->placeholders,
        ];

        // Remove optional fields that are not set
        foreach (['subject', 'replyTo', 'html', 'text', 'templateId', 'messageId', 'sendTime', 'tracking', 'listUnsubscribe', 'placeholders'] as $key) {
            if (empty($array[$key])) {
                unset($array[$key]);
            }
        }

        return $array;
    }
}
