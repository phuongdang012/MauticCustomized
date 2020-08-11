<?php

return [
    'name'        => 'Facebook Business',
    'description' => 'Enables integrations between Facebook Business and Mautic',
    'version'     => '1.0',
    'author'      => 'Tran Thanh Phuong Dang',

    'routes' => [
        'main' => [
            'mautic_facebook_business_pages' => [
                'path'       => '/plugin/facebook-business/manager/pages',
                'controller' => 'MauticFacebookBusinessBundle:FacebookPage:goToPageManager',
            ],
            'mautic_facebook_business_login' => [
                'path'       => '/plugin/facebook-business/login',
                'controller' => 'MauticFacebookBusinessBundle:FacebookCommon:goToLogin',
            ],
        ],
        'public' => [
            'mautic_facebook_business_auth_callback' => [
                'path'       => '/plugin/facebook-business/auth/callback',
                'controller' => 'MauticFacebookBusinessBundle:FacebookCommon:authCallback',
            ],
            'mautic_facebook_page_subscriber' => [
                'path'       => '/plugin/facebook-business/webhooks/page',
                'controller' => 'MauticFacebookBusinessBundle:FacebookPage:subscribe',
            ],
            'mautic_facebook_messenger_subscriber' => [
                'path'       => '/plugin/facebook-business/webhooks/messenger',
                'controller' => 'MauticFacebookBusinessBundle:FacebookMessenger:subscribe',
            ],
        ],
    ],
    'menu' => [
        'main' => [
            'mautic.plugin.facebook_business' => [
                'priority'  => 0,
                'id'        => 'mautic_facebook_business_root',
                'iconClass' => 'fa-facebook',
                'route'     => 'mautic_facebook_business_login',
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
    'services' => [
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
    ],
];
