<?php

namespace MauticPlugin\MauticFacebookBusinessBundle\Controller;

use FacebookAds\Api;
use Mautic\CoreBundle\Controller\CommonController;
use Symfony\Component\HttpFoundation\Response;

class FacebookPageController extends CommonController
{
    const INTEGRATION_NAME = 'FacebookBusiness';
    const APP_ID           = 'app_id';
    const APP_SECRET       = 'app_secret';

    private $keys;
    private $accessToken;

    public function __construct()
    {
        $integrationObj = $this->get('mautic.helper.integration')->getIntegrationObject($this::INTEGRATION_NAME);
        $this->keys     = $integrationObj->getDecryptedApiKeys();

        $session     = $this->get('session');
        $accessToken = $session->get('fb_access_token');
        if (null == $accessToken) {
        }
    }

    /**
     * Facebook Page lead form webhooks events.
     *
     * @return void
     */
    public function subscribeAction()
    {
        $webhookToken     = $this->request->get('hub.verify_token', '');
        $webhookChallenge = $this->request->get('hub.challenge', '');

        if ($webhookToken === $this->keys['verify_token']) {
            return new Response($webhookChallenge);
        }

        $content = $this->request->getContent();
        $data    = json_decode($content, true);

        //Get page token from user token

        //Get page from page token
    }

    public function grantPermissionOnPage()
    {
    }

    public function goToPageManager()
    {
    }

    public function getAllPagesComments()
    {
    }
}
