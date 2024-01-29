<?php

namespace Inmobile\InmobileSDK\Endpoints;

use Inmobile\InmobileSDK\InmobileApi;
use Inmobile\InmobileSDK\RequestModels\Email;
use Inmobile\InmobileSDK\Response;
use InvalidArgumentException;

class EmailsApi
{
    protected InmobileApi $api;

    public function __construct(InmobileApi $api)
    {
        $this->api = $api;
    }

    public function sendUsingTemplate(Email $email): Response
    {
        if (empty($email->getSender())) {
            throw new InvalidArgumentException('Sender is required when sending an email.');
        }

        if (empty($email->getRecipients())) {
            throw new InvalidArgumentException('Recipients are required when sending an email.');
        }

        if (empty($email->getTemplateId())) {
            throw new InvalidArgumentException('Template ID is required when sending using template.');
        }

        return $this->api->post('/email/outgoing/sendusingtemplate', $email->toArray());
    }

    public function send(Email $email): Response
    {
        if (empty($email->getSender())) {
            throw new InvalidArgumentException('Sender is required when sending an email.');
        }

        if (empty($email->getRecipients())) {
            throw new InvalidArgumentException('Recipients are required when sending an email.');
        }

        if (empty($email->getSubject())) {
            throw new InvalidArgumentException('Subject is required when sending an email.');
        }

        if (empty($email->getHtml())) {
            throw new InvalidArgumentException('HTML body is required when sending an email.');
        }

        if (!empty($email->getTemplateId())) {
            throw new InvalidArgumentException('Template ID is not allowed when sending an email without a template.');
        }

        if (!empty($email->getPlaceholders())) {
            throw new InvalidArgumentException('Placeholders are not allowed when sending an email without a template.');
        }

        return $this->api->post('/email/outgoing', $email->toArray());
    }

    public function getEvents(int $limit): Response
    {
        return $this->api->get('/email/outgoing/events', ['limit' => $limit]);
    }
}
