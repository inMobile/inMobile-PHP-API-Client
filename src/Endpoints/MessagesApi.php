<?php

namespace Inmobile\InmobileSDK\Endpoints;

use Inmobile\InmobileSDK\InmobileApi;
use Inmobile\InmobileSDK\RequestModels\Message;
use Inmobile\InmobileSDK\Response;

class MessagesApi
{
    protected InmobileApi $api;

    public function __construct(InmobileApi $api)
    {
        $this->api = $api;
    }

    /**
     * @param \Inmobile\InmobileSDK\RequestModels\Message|\Inmobile\InmobileSDK\RequestModels\Message[] $messages
     *
     * @return \Inmobile\InmobileSDK\Response
     */
    public function send($messages): Response
    {
        if (!is_array($messages)) {
            $messages = [$messages];
        }
        $messagesPayload = [];

        // Loop through and build the message payload.
        // This will handle any messages that is to be sent to multiple recipients.
        foreach ($messages as $message) {
            $recipients = (array) $message->getRecipient();

            foreach ($recipients as $recipient) {
                $messagesPayload[] = $message->to($recipient)->toArray();
            }
        }

        return $this->api->post('/sms/outgoing', ['messages' => $messagesPayload]);
    }

    public function sendUsingTemplate($messages, string $templateId): Response
    {
        if (!is_array($messages)) {
            $messages = [$messages];
        }

        $messagesPayload = [];

        // Loop through and build the message payload.
        // This will handle any messages that is to be sent to multiple recipients.
        foreach ($messages as $message) {
            $recipients = (array) $message->getRecipient();

            foreach ($recipients as $recipient) {
                $messagesPayload[] = $message->to($recipient)->toArray();
            }
        }

        return $this->api->post('/sms/outgoing/sendusingtemplate', ['templateId' => $templateId, 'messages' => $messagesPayload]);
    }

    public function getStatusReport(int $limit = 20): Response
    {
        return $this->api->get('/sms/outgoing/reports', ['limit' => $limit]);
    }

    public function sendUsingQuery(Message $message): Response
    {
        $messagePayload = $message->toArray();

        // Set boolean values to a string representation.
        // This is done because the endpoint does not accept boolean values.
        $messagePayload['flash'] = $messagePayload['flash'] ? 'true' : 'false';
        $messagePayload['respectBlacklist'] = $messagePayload['respectBlacklist'] ? 'true' : 'false';

        return $this->api->get('/sms/outgoing/sendusingquery', $messagePayload);
    }

    /**
     * @param string|array $messageIds
     *
     * @return \Inmobile\InmobileSDK\Response
     */
    public function cancel($messageIds): Response
    {
        if (!is_array($messageIds)) {
            $messageIds = [$messageIds];
        }

        return $this->api->post('/sms/outgoing/cancel', ['messageIds' => $messageIds]);
    }

    public function getIncoming(int $limit = 20): Response
    {
        return $this->api->get('/sms/incoming/messages', ['limit' => $limit]);
    }
}
