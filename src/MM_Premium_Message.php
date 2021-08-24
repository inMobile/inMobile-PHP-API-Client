<?php

namespace MarkVesterskov\InMobile;

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