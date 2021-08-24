<?php

namespace MarkVesterskov\InMobile;

class MM_Connector
{
    private $mmFrameworkVersion = 'MM_PHP_client_2_1_2_0';
    private $xml;
    private $error;
    private $reply;
    private $api_key;
    private $server_address;
    private $status_address;

    public function __construct($api_key, $server_address = null, $status_address = null)
    {
        $this->api_key = $api_key;
        $this->server_address = $server_address;
        $this->status_address = $status_address;

        $this->resetXmlElement();
    }

    private function resetXmlElement(){
        $this->xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><request/>');
        $this->xml->addAttribute('source', $this->mmFrameworkVersion);
        $authentication = $this->xml->addChild('authentication');
        $authentication->addAttribute('apikey', $this->api_key);
        $this->data = $this->xml->addChild('data');

        if(!is_null($this->status_address))
            $this->data->addChild('statuscallbackurl', $this->status_address);
    }

    public function addRefund(MM_Refund_Message $refundMessage)
    {
        $refund_element = $this->data->addChild('refundmessage ');
        $refund_element->addAttribute('messageIdToRefund', $refundMessage->getMessageIdToRefund());
        $text = $refund_element->addChild('text');
        $text->addCData($refundMessage->getMessage());
    }

    public function addMessage(MM_Message $mm_message)
    {
        $message_element = $this->AppendStandardXmlElements($mm_message);
        if($mm_message instanceof MM_Premium_Message){
            $this->AppendOverchargedXmlElements($message_element, $mm_message);
        }
    }

    private function AppendStandardXmlElements(MM_Message $mm_message)
    {
        $message_element = $this->data->addChild('message');

        $sendername = $message_element->addChild('sendername');
        $sendername->addCData($mm_message->getSendername());

        $text = $message_element->addChild('text');
        $text->addCData($mm_message->getMessage());

        // Flash defaults to false, no need to specify it
        if ($mm_message->getFlash() == true) {
            $text->addAttribute('flash', 'true');
        }
        $text->addAttribute('encoding', $mm_message->getEncoding());

        // Send time is optional
        if($mm_message->getSendTime() != '')
        {
            $message_element->addChild('sendtime', $mm_message->getSendTime());
        }

        // Expire In Seconds is optional
        if(is_null($mm_message->getExpireInSeconds()) == false)
        {
            $message_element->addChild('expireinseconds', $mm_message->getExpireInSeconds());
        }

        // Respect blacklist defaults to true, no need to specify it
        if($mm_message->getRespectBlacklist() == false)
        {
            $message_element->addChild('respectblacklist', 'false');
        }

        foreach($mm_message->getRecipients() as $recipient)
        {
            $recipients = $message_element->addChild('recipients');
            $recipients->addChild('msisdn', $recipient);
        }

        return $message_element;
    }

    private function AppendOverchargedXmlElements($message_element, MM_Premium_Message $mm_oc_message)
    {
        $overcharge = $message_element->addChild('overchargeinfo');

        $overcharge->addAttribute('price', $mm_oc_message->getPrice());
        $overcharge->addAttribute('type', $mm_oc_message->getType());
        $overcharge->addAttribute('countrycode', $mm_oc_message->getCountryCode());
        $overcharge->addAttribute('shortcode', $mm_oc_message->getShortcode());
        $overcharge->addAttribute('invoicedescription', $mm_oc_message->getInvoiceDescription());
    }

    public function send()
    {

        $x = curl_init($this->server_address.'/Api/V2/SendMessages');
        curl_setopt($x, CURLOPT_HEADER, 0);
        curl_setopt($x, CURLOPT_POST, 1);
        curl_setopt($x, CURLOPT_POSTFIELDS, array('xml' => $this->xml->asXML()));
        curl_setopt($x, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($x, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($x, CURLOPT_SSL_VERIFYPEER, false);
        $this->reply = curl_exec($x);
        curl_close($x);

        libxml_use_internal_errors(true);

        // After doing call, clear xml element to allow reuse of api client object
        $this->resetXmlElement();

        if(simplexml_load_string($this->reply))
        {
            return true;
        }

        else
        {
            $this->error = $this->reply;
            return false;
        }
    }

    public function getMessageIds()
    {
        $message_ids = simplexml_load_string($this->reply);

        return $message_ids;
    }

    public function getStatus()
    {
        $x = curl_init('https://'.$this->server_address.'/Api/V2/Get/GetMessageStatus?source='.$this->mmFrameworkVersion.'&apiKey='.$this->api_key);
        curl_setopt($x, CURLOPT_HEADER, 0);
        curl_setopt($x, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($x, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($x, CURLOPT_SSL_VERIFYPEER, false);
        $status_load = curl_exec($x);
        curl_close($x);

        $status_lines = explode("\n", $status_load);

        $return = array();

        foreach($status_lines as $status_line)
        {
            if($status_line)
            {
                $params = explode(":", $status_line);

                $return[] = array('id' => $params[0], 'status_code' => $params[1], 'status_description' => $params[2]);
            }
        }

        return $return;

    }

    public function getError()
    {
        return $this->error;
    }

}