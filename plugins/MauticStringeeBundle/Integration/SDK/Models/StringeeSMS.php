<?php

namespace MauticPlugin\MauticStringeeBundle\Integration\SDK\Client;

class StringeeSMS
{
    private $price;
    private $smsType;
    private $code;
    private $message;

    public function __construct($price = 0, $smsType = 0, $code = 0, $message = '')
    {
        $this->price   = $price;
        $this->smsType = $smsType;
        $this->code    = $code;
        $this->message = $message;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getSmsType()
    {
        return $this->smsType;
    }
}
