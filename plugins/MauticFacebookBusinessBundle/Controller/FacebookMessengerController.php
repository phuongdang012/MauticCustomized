<?php

namespace MauticPlugin\MauticFacebookBusinessBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;

class FacebookMessengerController extends CommonController
{
    const INTEGRATION_NAME = 'FacebookBusiness';
    const APP_ID           = 'app_id';
    const APP_SECRET       = 'app_secret';

    private $keys;

    public function __construct()
    {
        $integrationObj = $this->get('mautic.helper.integration')->getIntegrationObject($this::INTEGRATION_NAME);
        $accessToken    = $$integrationObj->session->get('fb_access_token');
        if (null == $accessToken) {
        }
        $this->keys = $integrationObj->getDecryptedApiKeys();
    }

    /**
     * Messenger webhooks function
     *
     * @return void
     */
    public function subscribeAction()
    {
    }

    /**
     * Login to facebook app
     *
     * @return string access_token
     */
    public function login()
    {
    }
}
