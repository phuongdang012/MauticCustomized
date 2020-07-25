<?php

namespace MauticPlugin\MauticStringeeBundle\Integration\Stringee\Client;

class Response
{
    private $headers;
    private $body;
    private $statusCode;

    public function __construct($headers = [], $body, $statusCode)
    {
        $this->headers    = $headers;
        $this->body       = $body;
        $this->statusCode = $statusCode;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getJsonBody()
    {
        return json_decode($this->body);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getHeader()
    {
        return $this->headers;
    }

    public function ok()
    {
        return $this->getStatusCode() < 400;
    }
}
