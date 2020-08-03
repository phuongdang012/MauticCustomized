<?php

namespace MauticPlugin\MauticStringeeBundle\Integration\SDK\Rest;

use Exception;
use MauticPlugin\MauticStringeeBundle\Integration\SDK\Client\CurlClient;

class Rest
{
    const ENV_SID    = 'SID';
    const ENV_SECRET = 'SECRET';
    const ENV_SENDER = 'SMS_SENDER';

    private $sid;
    private $secret;
    private $sender;
    private $curlClient;

    public function __construct($sid, $secret, $sender, $curlClient = null, $environment = null)
    {
        if (is_null($environment)) {
            $environment = $_ENV;
        }

        if ($sid) {
            $this->sid = $sid;
        } else {
            if (array_key_exists(self::ENV_SID, $environment)) {
                $this->sid = $environment[self::ENV_SID];
            }
        }

        if ($secret) {
            $this->secret = $secret;
        } else {
            if (array_key_exists(self::ENV_SECRET, $environment)) {
                $this->secret = $environment[self::ENV_SECRET];
            }
        }

        if ($sender) {
            $this->sender = $sender;
        } else {
            if (array_key_exists(self::ENV_SENDER, $environment)) {
                $this->sender = $environment[self::ENV_SENDER];
            }
        }

        if (!$this->sid || !$this->secret || !$this->sender) {
            throw new Exception('Credentials are required to create a Client');
        }

        if ($curlClient) {
            $this->curlClient = $curlClient;
        } else {
            $this->curlClient = new CurlClient($this->sid, $this->secret);
        }
    }

    /**
     * @param string $transactionId @param string $phoneNumber @param string $smsBody @param int $timeout
     *
     * @return Response
     */
    public function create($phoneNumber, $smsBody)
    {
        $url = 'https://api.stringee.com/v1/sms';

        $smsContent[] = [
            'from' => $this->sender,
            'to'   => $phoneNumber,
            'text' => $smsBody,
        ];

        $data = [
            'sms' => $smsContent,
        ];

        return $this->getClient()->request($url, 'POST', $data, [], 15);
    }

    public function getClient()
    {
        return $this->curlClient;
    }

    public function setClient(CurlClient $curlClient)
    {
        $this->curlClient = $curlClient;
    }
}
