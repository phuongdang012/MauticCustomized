<?php

namespace MauticPlugin\MauticVietGuysBundle\Integration;

use Mautic\PluginBundle\Helper\IntegrationHelper;
use VietGuys\SDK\Exceptions\VietGuysException;

class VietGuysConfiguration
{
    private $integrationHelper;
    private $sender;
    private $username;
    private $password;

    public function __construct(IntegrationHelper $integrationHelper)
    {
        $this->integrationHelper = $integrationHelper;
    }

    public function getSender()
    {
        $this->setConfiguration();

        return $this->sender;
    }

    public function getUsername()
    {
        $this->setConfiguration();

        return $this->username;
    }

    public function getPassword()
    {
        $this->setConfiguration();

        return $this->password;
    }

    private function setConfiguration()
    {
        if ($this->username && $this->password && $this->sender) {
            return;
        }

        $integration = $this->integrationHelper->getIntegrationObject('VietGuys');

        if (!$integration || !$integration->getIntegrationSettings()->getIsPublished()) {
            throw new VietGuysException('VietGuys plugin not enabled');
        }

        $keys = $integration->getDecryptedApiKeys();
        if (empty($keys['username']) || empty($keys['password']) || empty($keys['sender'])) {
            throw new VietGuysException('VietGuys required fields are not configured');
        }

        $this->username = $keys['username'];
        $this->password = $keys['password'];
        $this->sender   = $keys['sender'];
    }
}
