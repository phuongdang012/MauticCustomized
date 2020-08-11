<?php

namespace MauticPlugin\MauticFacebookBusinessBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;

class FacebookCommonController extends CommonController
{
    const INTEGRATION_NAME = 'FacebookBusiness';
    const APP_ID           = 'app_id';
    const APP_SECRET       = 'app_secret';

    private $keys;
    private $session;

    public function __construct()
    {
        if (null === $this->session) {
            $this->session = $this->get('session');
        }

        $integrationObj = $this->get('mautic.helper.integration')->getIntegrationObject($this::INTEGRATION_NAME);
        $accessToken    = $this->session->get('fb_access_token');
        if (null == $accessToken) {
        }
        $this->keys = $integrationObj->getDecryptedApiKeys();
    }

    public function goToLoginAction()
    {
        return $this->render('MauticFacebookBusinessBundle:Login:login.html.php');
    }

    public function authCallback()
    {
        $responseBody = json_decode($this->request->getContent());
        $accessToken  = $responseBody['access_token'];
        $this->session->set('fb_access_token', $accessToken);
    }
}
