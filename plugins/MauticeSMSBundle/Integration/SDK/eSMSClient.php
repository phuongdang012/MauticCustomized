<?php

namespace MauticPlugin\MauticeSMSBundle\Integration\SDK;

use Exception;
use Symfony\Component\HttpClient\HttpClient;

class eSMSClient
{
    private $httpClient;
    private $apiKey;
    private $secret;
    private $sender;
    private $smsType;

    public function __construct($apiKey, $secret, $smsType, $sender = null)
    {
        if (!$this->httpClient) {
            $this->httpClient = HttpClient::create();
        }
        $this->apiKey  = $apiKey;
        $this->secret  = $secret;
        $this->sender  = $sender;
        $this->smsType = $smsType;
    }

    public function create($phoneNumber, $smsBody)
    {
        $url = 'http://rest.esms.vn/MainService.svc/json/SendMultipleMessage_V4_post_json/';

        $options = [
            'body' => [
                'ApiKey'                                 => $this->apiKey,
                'SecretKey'                              => $this->secret,
                'Content'                                => $smsBody,
                'Phone'                                  => $phoneNumber,
                'SmsType'                                => $this->smsType,
                null == $this->sender ? '' : 'Brandname' => $this->sender,
            ],
        ];

        $response = $this->httpClient->request('POST', $url, $options);

        echo 'eSMS Response: ' + $response;
        echo $response->getContent();

        if (
            200 <= $response->getStatusCode() &&
            $response->getStatusCode() <= 300
        ) {
            $json = json_decode($response->getContent(), true);

            return new eSMSResponse(
                $json['CodeResult'],
                $json['CountRegenerate'],
                $json['SMSID']
            );
        }

        throw new Exception('Request error');
    }
}
