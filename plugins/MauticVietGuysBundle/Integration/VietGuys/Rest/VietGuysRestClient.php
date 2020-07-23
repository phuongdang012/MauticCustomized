<?php

namespace VietGuys\Rest;

use VietGuys\Client\CurlClient;
use VietGuys\Exception\VietGuysException;

class VietGuysRestClient
{
    const ENV_USERNAME = 'USERNAME';
    const ENV_PASSWORD = 'PASSWORD';
    const ENV_SENDER   = 'BRAND_NAME';

    private $username;
    private $password;
    private $sender;
    private $curlClient;

    /**
     * VietGuys constructor.
     *
     * @param string     $username
     * @param string     $password
     * @param string     $sender
     * @param CurlClient $curlClient  VietGuys CurlClient
     * @param mixed[]    $environment
     */
    public function __construct($username, $password, $sender, $curlClient = null, $environment = null)
    {
        if (is_null($environment)) {
            $environment = $_ENV;
        }

        if ($username) {
            $this->username = $username;
        } else {
            if (array_key_exists(self::ENV_USERNAME, $environment)) {
                $this->username = $environment[self::ENV_USERNAME];
            }
        }

        if ($password) {
            $this->password = $password;
        } else {
            if (array_key_exists(self::ENV_PASSWORD, $environment)) {
                $this->password = $environment[self::ENV_PASSWORD];
            }
        }

        if ($sender) {
            $this->sender = $sender;
        } else {
            if (array_key_exists(self::ENV_SENDER, $environment)) {
                $this->sender = $environment[self::ENV_SENDER];
            }
        }

        if (!$this->username || !$this->password || !$this->sender) {
            throw new VietGuysException('Credentials are required to create a Client');
        }

        if ($curlClient) {
            $this->curlClient = $curlClient;
        } else {
            $this->curlClient = new CurlClient();
        }
    }

    /**
     * @param string $transactionId @param string $phoneNumber @param string $smsBody @param int $timeout
     *
     * @return Response
     */
    public function create($transactionId, $phoneNumber, $smsBody, $timeout = null)
    {
        $data = [
            'u'     => $this->username,
            'pwd'   => $this->password,
            'from'  => $this->sender,
            'phone' => $phoneNumber,
            'sms'   => $smsBody,
            'bid'   => $transactionId,
            'type'  => 1,
            'json'  => 0,
        ];

        return $this->getClient()->request(
            'https://cloudsms.vietguys.biz:4438/api/index.php',
            'POST',
            $data,
            [],
            $timeout
        );
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
