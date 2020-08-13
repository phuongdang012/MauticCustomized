<?php

namespace MauticPlugin\MauticFacebookBusinessBundle\Integration;

use FacebookAds\Api;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use MauticPlugin\MauticSocialBundle\Form\Type\FacebookType;

class FacebookBusinessIntegration extends AbstractIntegration
{
    private const APP_ID = 'app_id';
    private const APP_SECRET = 'app_secret';

    public function getName()
    {
        return 'FacebookBusiness';
    }

    public function getDisplayName()
    {
        return 'Facebook Business';
    }

    public function getIcon()
    {
        return 'plugins/MauticFacebookBusinessBundle/Assets/img/facebook.png';
    }

    public function getAuthenticationType()
    {
        return 'oauth2';
    }

    public function getIdentifierFields()
    {
        return [
            'facebook_business',
        ];
    }

    public function getRequiredKeyFields()
    {
        return [
            'app_id'     => 'mautic.plugin.facebook_business.config.form.app_id',
            'app_secret' => 'mautic.plugin.facebook_business.config.form.app_secret',
        ];
    }

    public function parseCallbackResponse($data, $postAuthorization = false)
    {
        $jsonObj = json_decode($data, true);

        if ($postAuthorization) {
            $keys = $this->getDecryptedApiKeys();
            Api::init($keys[$this::APP_ID], $keys[$this::APP_SECRET], $jsonObj['access_token']);

            //Retrieve user long-lived token
            Api::instance()->call()

            //Retrieve page long-lived token
            $response = Api::instance()->call('/me/accounts');
            $accountData = $response->getContent();
            $accounts = [];
            foreach ($accountData['data'] as $page) {
                $accounts[$page['id']] = $page['access_token'];
            }
            $this->session->set('page_access_token', $accounts);
        }

        return $jsonObj;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthenticationUrl()
    {
        return 'https://www.facebook.com/dialog/oauth';
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenUrl()
    {
        return 'https://graph.facebook.com/oauth/access_token';
    }

    /**
     * @return string
     */
    public function getAuthScope()
    {
        return 'email';
    }

    /**
     * @param $endpoint
     *
     * @return string
     */
    public function getApiUrl($endpoint)
    {
        return "https://graph.facebook.com/$endpoint";
    }

    /**
     * Get available company fields for choices in the config UI.
     *
     * @param array $settings
     *
     * @return array
     */
    public function getFormCompanyFields($settings = [])
    {
        return [];
    }

    /**
     * @param array $settings
     *
     * @return array|mixed
     */
    public function getFormLeadFields($settings = [])
    {
        return $this->getFormFieldsByObject('contacts', $settings);
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableLeadFields($settings = [])
    {
        $fields = [
            'about'         => ['type' => 'string'],
            'birthday'      => ['type' => 'string'],
            'email'         => ['type' => 'string'],
            'work_email'    => ['type' => 'string'],
            'company_name'  => ['type' => 'string'],
            'first_name'    => ['type' => 'string'],
            'gender'        => ['type' => 'string'],
            'last_name'     => ['type' => 'string'],
            'locale'        => ['type' => 'string'],
            'middle_name'   => ['type' => 'string'],
            'name'          => ['type' => 'string'],
            'timezone'      => ['type' => 'string'],
            'website'       => ['type' => 'string'],
        ];

        foreach ($fields as $field_id => $field) {
            $fields[$field_id]['label'] = $this->translator->trans('mautic.integration.facebook_business.' . $field_id);
        }

        return $fields;
    }

    /**
     * @param $object
     *
     * @return array|mixed
     */
    protected function getFormFieldsByObject($object, $settings = [])
    {
        $settings['feature_settings']['objects'] = [$object => $object];
        return $this->getAvailableLeadFields($settings);
    }

    public function mapLead($data)
    {
        return $this->matchUpData($data);
    }
}
