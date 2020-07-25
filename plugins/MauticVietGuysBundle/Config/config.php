<?php
/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

return [
    'name'        => 'VietGuys',
    'description' => 'Enables integration with Mautic supported VietGuys collaboration products.',
    'version'     => '1.0',
    'author'      => 'Tran Thanh Phuong Dang',

    'services' => [
        'integrations' => [
            'mautic.integration.vietguys' => [
                'class'     => \MauticPlugin\MauticVietGuysBundle\Integration\VietGuysIntegration::class,
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
        // 'helper' => [
        //     'vietguys.rest_client' => [
        //         'class' => \VietGuys\SDK\Rest\RestClient::class,
        //         'arguments' => [],
        //     ],

        //     'vietguys.client' => [
        //         'class' => \VietGuys\SDK\Client\CurlClient::class,
        //         'arguments' => [],
        //     ],

        //     'vietguys.model.response' => [
        //         'class' => \VietGuys\SDK\Client\Response::class,
        //         'arguments' => [],
        //     ],

        //     'vietguys.exception' => [
        //         'class' => \VietGuys\SDK\Exceptions\VietGuysException::class,
        //         'arguments' => [],
        //     ],
        // ],
        'other' => [
            'mautic.sms.vietguys.configuration' => [
                'class'        => \MauticPlugin\MauticVietGuysBundle\Integration\VietGuysConfiguration::class,
                'arguments'    => [
                    'mautic.helper.integration',
                ],
            ],
            'mautic.sms.vietguys.transport' => [
                'class'        => \MauticPlugin\MauticVietGuysBundle\Integration\VietGuysTransport::class,
                'arguments'    => [
                    'mautic.sms.vietguys.configuration',
                    'monolog.logger.mautic',
                ],
                'tag'          => 'mautic.sms_transport',
                'tagArguments' => [
                    'integrationAlias' => 'VietGuys',
                ],
                'serviceAliases' => [
                    'sms_api',
                    'mautic.sms.api',
                ],
            ],
        ],
    ],
];
