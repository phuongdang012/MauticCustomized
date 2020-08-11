<?php

namespace MauticPlugin\MauticFacebookBusinessBundle\Integration;

use FacebookAds\Api;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use MauticPlugin\MauticSocialBundle\Form\Type\FacebookType;

class FacebookBusinessIntegration extends AbstractIntegration
{
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
        return 'none';
    }

    public function getRequiredKeyFields()
    {
        return [
            'app_id'     => 'mautic.plugin.facebook_business.config.form.app_id',
            'app_secret' => 'mautic.plugin.facebook_business.config.form.app_secret',
        ];
    }

    // public function getAuthenticationUrl()
    // {
    //     return 'https://www.facebook.com/dialog/oauth';
    // }

    // /**
    //  * {@inheritdoc}
    //  */
    // public function getAccessTokenUrl()
    // {
    //     return 'https://graph.facebook.com/oauth/access_token';
    // }

    // public function getApiUrl($endpoint)
    // {
    //     return "https://graph.facebook.com/$endpoint";
    // }

    // public function getFormType()
    // {
    //     return FacebookType::class;
    // }

    // public function parseCallbackResponse($data, $postAuthorization = false)
    // {
    //     // Facebook is inconsistent in that it returns errors as json and data as parameter list
    //     $values = parent::parseCallbackResponse($data, $postAuthorization);

    //     if (null === $values) {
    //         parse_str($data, $values);

    //         $this->session->set('facebook_access_token', $values);
    //     }

    //     return $values;
    // }
}
