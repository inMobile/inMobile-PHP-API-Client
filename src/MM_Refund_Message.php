<?php

namespace MarkVesterskov\InMobile;

class MM_Refund_Message
{
    private $message = '';
    private $message_id_to_refund = '';

    public function __construct($message, $message_id_to_refund)
    {
        $this->message = $message;
        $this->message_id_to_refund = $message_id_to_refund;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getMessageIdToRefund()
    {
        return $this->message_id_to_refund;
    }
}