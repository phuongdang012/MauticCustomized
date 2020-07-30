<?php

return [
    'name'        => 'eSMS',
    'description' => 'Enables integration with Mautic supported eSMS collaboration products.',
    'version'     => '1.0',
    'author'      => 'Tran Thanh Phuong Dang',

    'services' => [
        'integrations' => [
            'mautic.integration.esms' => [
                'class'     => \MauticPlugin\MauticeSMSBundle\Integration\eSMSIntegration::class,
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
            'mautic.esms.configuration' => [
                'class'        => \MauticPlugin\MauticeSMSBundle\Integration\eSMSConfiguration::class,
                'arguments'    => [
                    'mautic.helper.integration',
                ],
            ],
            'mautic.esms.transport' => [
                'class'        => \MauticPlugin\MauticeSMSBundle\Integration\eSMSTransport::class,
                'arguments'    => [
                    'mautic.esms.configuration',
                    'monolog.logger.mautic',
                ],
                'tag'          => 'mautic.sms_transport',
                'tagArguments' => [
                    'integrationAlias' => 'eSMS',
                ],
                'serviceAliases' => [
                    'sms_api',
                    'mautic.sms.api',
                ],
            ],
        ],
    ],
];
