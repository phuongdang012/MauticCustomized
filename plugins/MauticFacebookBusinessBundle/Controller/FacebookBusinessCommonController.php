<?php

namespace MauticPlugin\MauticFacebookBusinessBundle\Controller;

use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use Mautic\CoreBundle\Controller\CommonController;
use Symfony\Component\HttpFoundation\Response;

class FacebookBusinessCommonController extends CommonController
{
    const APP_ID = 'app_id';
    const APP_SECRET = 'app_secret';
    const ADS_ACCOUNT_ID = 'ads_account_id';

    private $keys;

    public function __construct()
    {
        if (empty($this->keys)) {
            $integrationName = 'FacebookBusiness';
            $integrationHelper = $this->get('mautic.helper.integration');

            $integrationObj = $integrationHelper->getIntegrationObject($integrationName);

            $this->keys = $integrationObj->getDecryptedApiKeys();
        }
    }

    /**
     * @Route('/plugin/facebook-business/page/webhooks')
     */
    public function subscribePageAction()
    {
        $challenge = $this->request->get('hub.challenge', '');
        $verifyToken = $this->request->get('hub.verify_token', '');

        if ($verifyToken === $this->keys['verify_token']) {
            return new Response($challenge);
        }

        $content = $this->request->getContent();
        $data = json_decode($content, true);

        $accessToken = $this->getAccessToken();
        $api = Api::init(
            $this->keys[$this::APP_ID],
            $this->keys[$this::APP_SECRET],
            $accessToken
        );
        $accountId = 'act_' . $this->keys[$this::ADS_ACCOUNT_ID];

        $account = new AdAccount($accountId);
        $fbLeadGenForms = $account->get();
        $fbFormNames = [];
        foreach ($fbLeadGenForms as $form) {
            $formData = $form->getData();
            $fbFormNames[$formData['id']]
        }
    }

    /**
     * @Route('/plugin/facebook-business/messenger/webhooks')
     */
    public function subscribeMessengerAction()
    {
    }

    private function getAccessToken()
    {
    }
}
