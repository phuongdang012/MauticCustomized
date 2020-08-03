<?php

namespace MauticPlugin\MauticVietGuysBundle\Integration\VietGuys\SDK\Client;

class VietGuysResponse
{
    protected $headers;
    protected $content;
    protected $statusCode;
    private $returnedCode = [
        -1 => 'Paramater missing',
        -2 => 'VietGuys server busy',
        -3, -5 => 'Authorization failed',
        -4 => 'Account locked',
        -6 => 'API feature not activated',
        -7 => 'IP limitations',
        -8 => "'From' parameter not permitted. Contact VietGuys for assistance",
        -9, -12 => 'Insufficient balance',
        -10 => 'Phone number not valid',
        -11 => 'Phone number on blacklist, not available to receive message',
    ];

    public function __construct($statusCode, $content, $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->content    = $content;
        $this->headers    = $headers;
    }

    public function getContent()
    {
        return json_decode($this->content, true);
    }

    public function translateResultFrom($code)
    {
        if (array_key_exists($code, $this->returnedCode)) {
            return $this->returnedCode[$code];
        }

        return 'SMS sent succesfully';
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function ok()
    {
        return $this->getStatusCode() < 400;
    }

    public function __toString()
    {
        return '[VietGuysResponse] HTTP '.$this->getStatusCode().' '.$this->content;
    }
}
