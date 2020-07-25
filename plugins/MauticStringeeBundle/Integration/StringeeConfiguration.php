<?php

namespace MauticPlugin\MauticStringeeBundle\Integration;

use Exception;
use Mautic\PluginBundle\Helper\IntegrationHelper;

class StringeeConfiguration
{
    private $integrationHelper;
    private $sid;
    private $secret;
    private $sender;

    public function __construct(IntegrationHelper $integrationHelper)
    {
        $this->integrationHelper = $integrationHelper;
    }

    public function getSid()
    {
        $this->setConfiguration();

        return $this->sid;
    }

    public function getSecret()
    {
        $this->setConfiguration();

        return $this->secret;
    }

    public function getSender()
    {
        $this->setConfiguration();

        return $this->sender;
    }

    private function setConfiguration()
    {
        if ($this->sid && $this->secret && $this->sender) {
            return;
        }

        $integration = $this->integrationHelper->getIntegrationObject('Stringee');

        if (!$integration || !$integration->getIntegrationSettings()->getIsPublished()) {
            throw new Exception('Stringee plugin not enabled');
        }

        $keys = $integration->getDecryptedApiKeys();
        if (empty($keys['sid']) || empty($keys['secret']) || empty($keys['sender'])) {
            throw new Exception('Stringee required fields are not configured');
        }

        $this->sid    = $keys['sid'];
        $this->secret = $keys['secret'];
        $this->sender = $keys['sender'];
    }
}
