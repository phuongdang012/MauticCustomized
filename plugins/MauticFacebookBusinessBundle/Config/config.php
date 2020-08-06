<?php

return [
    'routes' => [
        'main' => [
            'mautic_facebook_business_home' => [
                'path'       => '/plugin/facebook-business/home',
                'controller' => 'MauticFacebookBusinessBundle:FacebookBusiness:goToHome',
            ],
            'mautic_facebook_business_pages' => [
                'path'       => '/plugin/facebook-business/pages',
                'controller' => 'MauticFacebookBusinessBundle:FacebookBusiness:goToPagesManager',
            ],
        ],
        'public' => [
            'mautic_facebook_business_auth_callback' => [
                'path'       => '/plugin/facebook-business/auth/callback',
                'controller' => 'MauticFacebookBusinessBundle:FacebookBusiness:loginCallback',
            ],
            'mautic_facebook_subscriber' => [
                'path'       => '/plugin/facebook-business/webhooks',
                'controller' => 'MauticFacebookBusinessBundle:FacebookBusiness:subscribeLead',
            ],
        ],
    ],
    'menu' => [
        'main' => [
            'mautic.plugin.facebook_business' => [
                'priority'  => 0,
                'id'        => 'mautic_facebook_business_root',
                'iconClass' => 'fa-facebook',
                'route'     => 'mautic_facebook_business_home',
                'checks'    => [
                    'integration' => [
                        'FacebookBusiness' => [
                            'enabled' => true,
                        ],
                    ],
                ],
            ],
            'mautic.plugin.facebook_business.pages' => [
                'priority' => 10,
                'route'    => 'mautic_facebook_business_pages',
                'parent'   => 'mautic.plugin.facebook_business',
            ],
        ],
    ],
    'integrations' => [
        'mautic.integration.facebook_business' => [
            'class'     => \MauticPlugin\MauticFacebookBusinessBundle\Integration\FacebookBusinessIntegration::class,
            'arguments' => [
                'event_dispatcher',
                'mautic.helper.cache_storage',
                'doctrine.orm.entity_manager',
                'session',
                'request_stack',
                'router',
                'translator',
                'logger',
                'mautic.helper.encryption',
                'mautic.lead.model.lead',
                'mautic.lead.model.company',
                'mautic.helper.paths',
                'mautic.core.model.notification',
                'mautic.lead.model.field',
                'mautic.plugin.model.integration_entity',
                'mautic.lead.model.dnc',
                'mautic.helper.integration',
            ],
        ],
    ],
];
