<?php

namespace MarkVesterskov\InMobile;

class MM_Message
{
    private $message = '';
    private $sendername = '';
    private $recipients = array();

    // Optional fields
    private $send_time = '';
    private $flash = false;
    private $encoding = '';
    private $expire_in_seconds = null;
    private $respect_blacklist = true;

    public function __construct($message, array $recipients, $sendername)
    {
        $this->message = $message;
        $this->recipients = $recipients;
        $this->sendername = $sendername;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getRecipients()
    {
        return $this->recipients;
    }

    public function getSendername()
    {
        return $this->sendername;
    }

    public function getSendTime()
    {
        return $this->send_time;
    }

    public function setSendTime($send_time)
    {
        $this->send_time = $send_time;
    }

    public function getRespectBlacklist()
    {
        return $this->respect_blacklist;
    }

    public function setRespectBlacklist($respect_blacklist)
    {
        $this->respect_blacklist = $respect_blacklist;
    }

    public function getFlash()
    {
        return $this->flash;
    }

    public function setFlash($flash)
    {
        $this->flash = $flash;
    }

    public function getEncoding()
    {
        return $this->encoding;
    }

    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    public function getExpireInSeconds()
    {
        return $this->expire_in_seconds;
    }

    public function setExpireInSeconds($expire_in_seconds)
    {
        $this->expire_in_seconds = $expire_in_seconds;
    }
}