<?php

namespace MauticPlugin\MauticVietGuysBundle\Integration\SDK\Client;

use MauticPlugin\MauticVietGuysBundle\Integration\SDK\Exceptions\VietGuysException;
use MauticPlugin\MauticVietGuysBundle\Integration\VietGuys\SDK\Client\VietGuysResponse;
use Symfony\Component\HttpClient\HttpClient;

class RestClient
{
    const ENV_USERNAME = 'USERNAME';
    const ENV_PASSWORD = 'PASSWORD';
    const ENV_SENDER   = 'BRAND_NAME';

    private $username;
    private $password;
    private $sender;
    private $httpClient;

    /**
     * VietGuys constructor.
     *
     * @param string     $username
     * @param string     $password
     * @param string     $sender
     * @param mixed[]    $environment
     */
    public function __construct($username, $password, $sender, $environment = null)
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

        if (!$this->httpClient) {
            $this->httpClient = HttpClient::create();
        }
    }

    /**
     * @param string $transactionId @param string $phoneNumber @param string $smsBody @param int $timeout
     *
     * @return VietGuysResponse
     */
    public function create($transactionId, $phoneNumber, $smsBody)
    {
        $requestOptions = [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode([
                'u'     => $this->username,
                'pwd'   => $this->password,
                'from'  => $this->sender,
                'phone' => $phoneNumber,
                'sms'   => $smsBody,
                'bid'   => $transactionId,
                'type'  => 1,
                'json'  => 0,
            ])
        ];

        $jsonResponse = $this->getClient()->request(
            'POST',
            'https://cloudsms.vietguys.biz:4438/api/index.php',
            $requestOptions
        );

        $vietGuysResponse = new VietGuysResponse(
            $jsonResponse->getStatusCode(),
            $jsonResponse->getContent(),
            $jsonResponse->getHeaders()
        );

        if (200 <= $vietGuysResponse->getStatusCode() && $vietGuysResponse->getStatusCode() <= 300) {
            return $vietGuysResponse;
        }

        throw new VietGuysException("Request failed:" + $vietGuysResponse->getStatusCode());
    }

    private function getClient()
    {
        return $this->httpClient;
    }
}
