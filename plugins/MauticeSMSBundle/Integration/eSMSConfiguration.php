<?php

namespace MauticPlugin\MauticeSMSBundle\Integration;

use Exception;
use Mautic\PluginBundle\Helper\IntegrationHelper;

class eSMSConfiguration
{
    private $integrationHelper;
    private $sender;
    private $apiKey;
    private $secret;

    public function __construct($integrationHelper)
    {
        $this->integrationHelper = $integrationHelper;
    }

    public function getSenderType()
    {
        $this->setConfiguration();

        return null === $this->sender ? 8 : 4;
    }

    public function getSender()
    {
        $this->setConfiguration();

        return $this->sender;
    }

    public function getApiKey()
    {
        $this->setConfiguration();

        return $this->apiKey;
    }

    public function getSecret()
    {
        $this->setConfiguration();

        return $this->secret;
    }

    private function setConfiguration()
    {
        if ($this->apiKey && $this->secret) {
            return;
        }

        $integration = $this->integrationHelper->getIntegrationObject('eSMS');

        if (!$integration || $integration->getIntegrationSettings()->getIsPublished()) {
            throw new Exception('eSMS plugin not enabled');
        }

        $keys = $integration->getDecryptedApiKeys();
        if (empty($keys['api_key']) || empty($keys['secret'])) {
            throw new Exception('eSMS required fields are not configured');
        }

        $this->apiKey = $keys['api_key'];
        $this->secret = $keys['secret'];
        $this->sender = empty($keys['sender']) ? null : $keys['sender'];
    }
}
