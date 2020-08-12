<?php

namespace MauticPlugin\MauticFacebookBusinessBundle\Controller;

use FacebookAds\Api;
use Mautic\CoreBundle\Controller\CommonController;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\MauticFacebookBusinessBundle\Helpers\Helper;
use MauticPlugin\MauticFacebookBusinessBundle\Model\Webhooks\PostComment;
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

        $content     = $this->request->getContent();
        $response    = json_decode($content, true);
        $postComment = PostComment::parseToObjFrom($response['entry'][0]);

        if (count(Helper::extractPhoneNumber($postComment->getMessage())) > 0) {
            $lead = new Lead();
            $lead->setNewlyCreated(true);
            $lead->setFirstName($postComment->getCommenterName());
            $lead->setPhone(Helper::extractPhoneNumber($postComment->getMessage()));
            $lead->setId($postComment->getCommenterId());
            $leadFields = [
                'firstName' => $lead->getFirstName(),
                'phone'     => $lead->getPhone(),
                'id'        => $lead->getId(),
            ];
            $leadModel = $this->getModel('lead');

            $uniqueLeadFields    = $this->getModel('lead.field')->getUniqueIdentiferFields();
            $uniqueLeadFieldData = [];

            $inList = array_intersect_key($leadFields, $uniqueLeadFields);
            foreach ($inList as $key => $value) {
                if (empty($query[$key])) {
                    unset($inList[$key]);
                }

                if (array_key_exists($key, $uniqueLeadFields)) {
                    $uniqueLeadFieldData[$key] = $value;
                }
            }
            if (count($inList) && count($uniqueLeadFieldData)) {
                $existingLeads = $this->getDoctrine()->getManager()->getRepository('MauticLeadBundle:Lead')->getLeadsByUniqueFields($uniqueLeadFieldData, $leadId);
                if (!empty($existingLeads)) {
                    // Existing found so merge the two leads
                    $lead = $leadModel->mergeLeads($lead, $existingLeads[0]);
                }
                $leadIpAddresses = $lead->getIpAddresses();
                if (!$leadIpAddresses->contains($ipAddress)) {
                    $lead->addIpAddress($ipAddress);
                }
            }
            $leadModel->setFieldValues($lead, $leadFields);
            $leadModel->saveEntity();
            $leadModel->setCurrentLead($lead);
        }
    }

    public function goToPageManager()
    {
        return $this->render('MauticFacebookBusinessBundle:PageManager:page_manager.html.php');
    }

    public function getAllPagesComments()
    {
    }
}
