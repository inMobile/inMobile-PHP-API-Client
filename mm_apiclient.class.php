<?php 

class SimpleXMLExtended extends SimpleXMLElement 
{ 
	public function addCData($cdata_text)
	{ 
		$node = dom_import_simplexml($this); 
		$no = $node->ownerDocument; 
		$node->appendChild($no->createCDATASection($cdata_text)); 
	} 
}

class MM_Connector
{
    private $mmFrameworkVersion = 'MM_PHP_client_2_1_1_0';
	private $xml;
	private $error;
	private $reply;
	private $api_key;
	private $server_address;
	private $status_address;

	public function __construct($api_key, $server_address, $status_address = null)
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
		
		$message_element->addChild('sendername', $mm_message->getSendername());
		$text = $message_element->addChild('text');

		// Flash defaults to false, no need to specify it
		if ($mm_message->getFlash() == true) {
			$text->addAttribute('flash', 'true');
		}
		$text->addAttribute('encoding', $mm_message->getEncoding());
		
		$text->addCData($mm_message->getMessage());

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

class MM_Premium_Message extends MM_Message
{
	private $price = '';
	private $type = '';
	private $country_code = '';
	private $shortcode = '';
	private $invoice_description = '';

	public function __construct($message, array $recipients, $sendername, $price, $type, $country_code, $shortcode, $invoice_description)
	{
		parent::__construct($message, $recipients, $sendername);

		$this->price = $price;
		$this->type = $type;
		$this->country_code = $country_code;
		$this->shortcode = $shortcode;
		$this->invoice_description = $invoice_description;
	}

	public function getPrice()
	{
		return $this->price;
	}

	public function getType()
	{
		return $this->type;
	}

	public function getCountryCode()
	{
		return $this->country_code;
	}

	public function getShortcode()
	{
		return $this->shortcode;
	}

	public function getInvoiceDescription()
	{
		return $this->invoice_description;
	}
}

?>
