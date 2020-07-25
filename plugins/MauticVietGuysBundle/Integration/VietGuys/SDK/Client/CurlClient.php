<?php

namespace MauticPlugin\MauticVietGuysBundle\Integration\VietGuys\SDK\Client;

use MauticPlugin\MauticVietGuysBundle\Integration\VietGuys\SDK\Client\Response;
use MauticPlugin\MauticVietGuysBundle\Integration\VietGuys\SDK\Exceptions\VietGuysException;

class CurlClient
{
    // const ENV_ACCOUNT_SID = "VIETGUYS_USERNAME";
    // const ENV_AUTH_TOKEN = "VIETGUYS_PASSWORD";

    const DEFAULT_TIMEOUT = 3600;

    protected $username;
    protected $password;
    protected $senders;
    protected $options = [];

    public function __construct($username = null, $password = null, $sender = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->senders  = $sender;
    }

    public function setOption(array $options)
    {
        $this->options = $options;
    }

    public function post($url, $data, $timeout = null)
    {
        return $this->request($url, 'POST', $data, [], $timeout);
    }

    public function get($url, $timeout = null)
    {
        return $this->request($url, 'GET', [], [], $timeout);
    }

    public function request($url, $method, $data, $headers, $timeout = null)
    {
        $curl = curl_init($url);

        $timeout = is_null($timeout) ? self::DEFAULT_TIMEOUT : $timeout;

        $option = [];

        $options = $this->options + [
            CURLOPT_HEADER         => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_INFILESIZE     => null,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT => $timeout,
        ];

        //Header
        foreach ($headers as $key => $value) {
            $options[CURLOPT_HTTPHEADER][] = "$key: $value";
        }

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
                        throw new VietGuysException('Unable to open a temporary file');
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

            //START:         PARSE DATA SECTION============================================
            $parts = explode("\r\n\r\n", $response, 3);

            list($head, $responseBody) = ('HTTP/1.1 100 Continue' == $parts[0]) ? [$parts[1], $parts[2]] : [$parts[0], $parts[1]];
            $responseHeaders           = [];
            $headerLines               = explode("\r\n", $head);
            array_shift($headerLines);
            foreach ($headerLines as $line) {
                list($key, $value)    = explode(':', $line, 2);
                $resposeHeaders[$key] = $value;
            }
            //END:           PARSE DATA SECTION==============================================
        } else {
            $statusCode = $error;
        }

        //CLOSE CONNECTIONS================================
        curl_close($curl);
        if (isset($buffer) && is_resource($buffer)) {
            fclose($buffer);
        }

        return new Response($statusCode, $responseBody, $responseHeaders);
    }
}
