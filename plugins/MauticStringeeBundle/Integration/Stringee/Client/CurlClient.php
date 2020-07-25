<?php

namespace MauticPlugin\MauticStringeeBundle\Integration\Stringee\Client;

use Exception;
use Firebase\JWT\JWT;
use MauticPlugin\MauticStringeeBundle\Integration\Stringee\Client\Response;

class CurlClient
{
    const REQUEST_TIMEOUT = 60;

    protected $options = [];
    protected $keySid;
    protected $keySecret;
    protected $tokenExpiry;

    public function __construct($keySid, $keySecret, $tokenExpiry = 36000)
    {
        $this->keySid      = $keySid;
        $this->keySecret   = $keySecret;
        $this->tokenExpiry = $tokenExpiry;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function post($url, $data, $requestTimeout = null)
    {
        return $this->request($url, 'POST', $data, [], $requestTimeout);
    }

    public function generateAuthToken()
    {
        $now    = time();
        $expiry = $now + $this->tokenExpiry;

        $header  = ['cty' => 'stringee-api;v=1'];
        $payload = [
            'jti'      => $this->keySid.'-'.$now,
            'iss'      => $this->keySid,
            'exp'      => $expiry,
            'rest_api' => true,
        ];

        $token = JWT::encode($payload, $this->keySecret, 'HS256', null, $header);

        return $token;
    }

    public function request($url, $method, $data = null, $headers = [], $timeout = null)
    {
        $curl = curl_init($url);

        $timeout = is_null($timeout) ? self::REQUEST_TIMEOUT : $timeout;

        $options = [];

        $options = $this->options + [
            CURLOPT_HEADER         => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_INFILESIZE     => null,
            CURLOPT_HTTPHEADER     => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
            CURLOPT_TIMEOUT => $timeout,
        ];

        //Add JWT token to request header
        foreach ($headers as $key => $value) {
            $options[CURLOPT_HTTPHEADER][] = "$key: $value";
        }

        $jwtStr                        = $this->generateAuthToken();
        $options[CURLOPT_HTTPHEADER][] = 'X-STRINGEE-AUTH: '.$jwtStr;

        switch (strtolower(trim($method))) {
            case 'get':
                $options[CURLOPT_HTTPGET] = true;
                break;

            case 'post':
                $options[CURLOPT_POST]       = true;
                $options[CURLOPT_POSTFIELDS] = $data;
                break;

            case 'put':
                $options[CURLOPT_PUT] = true;
                if ($data) {
                    if ($buffer = fopen('php://memory', 'w+')) {
                        fwrite($buffer, $data);
                        fseek($buffer, 0);
                        $options[CURLOPT_INFILE]     = $buffer;
                        $options[CURLOPT_INFILESIZE] = strlen($data);
                    } else {
                        throw new Exception('Unable to open temp file');
                    }
                }
                break;
            case 'head':
                $options[CURLOPT_NOBODY] = true;
                break;
            default:
                $options[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
        }

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $error    = curl_errno($curl);

        if (!$error) {
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            $parts = explode("\r\n\r\n", $response, 3);

            list($head, $responseBody) = ('HTTP/1.1 100 Continue' == $parts[0]) ? [$parts[1], $parts[2]] : [$parts[0], $parts[1]];

            $responseHeaders = [];
            $headerLines     = explode("\r\n", $head);
            array_shift($headerLines);
            foreach ($headerLines as $line) {
                list($key, $value)     = explode(':', $line, 2);
                $responseHeaders[$key] = $value;
            }
        } else {
            $statusCode = $error;
        }

        curl_close($curl);
        if (isset($buffer) && is_resource($buffer)) {
            fclose($buffer);
        }

        return new Response($statusCode, $responseBody, $responseHeaders);
    }
}
