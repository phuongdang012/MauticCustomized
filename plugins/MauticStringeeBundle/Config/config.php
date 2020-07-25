<?php

return [
    'name'        => 'Stringee',
    'description' => 'Enables integration with Stringee API',
    'version'     => '1.0',
    'author'      => 'Tran Thanh Phuong Dang',

    'services' => [
        'integrations' => [
            'mautic.integration.stringee' => [
                'class'     => \MauticPlugin\MauticStringeeBundle\Integration\StringeeIntegration::class,
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
                ],
            ],
        ],

        'other' => [
            'mautic.sms.stringee.configuration' => [
                'class'        => \MauticPlugin\MauticStringeeBundle\Integration\StringeeConfiguration::class,
                'arguments'    => [
                    'mautic.helper.integration',
                ],
            ],

            'mautic.sms.stringee.transport' => [
                'class'        => \MauticPlugin\MauticStringeeBundle\Integration\StringeeTransport::class,
                'arguments'    => [
                    'mautic.sms.stringee.configuration',
                    'monolog.logger.mautic',
                ],
                'tag'          => 'mautic.sms_transport',
                'tagArguments' => [
                    'integrationAlias' => 'Stringee',
                ],
                'serviceAliases' => [
                    'sms_api',
                    'mautic.sms.api',
                ],
            ],
        ],
    ],
];
