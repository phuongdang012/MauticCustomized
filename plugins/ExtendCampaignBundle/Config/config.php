<?php

return [
    'name'        => 'Extend Campaign',
    'description' => 'Extend package for Mautic campaign',
    'version'     => '1.0',
    'author'      => 'Tran Thanh Phuong Dang',

    'services' => [
        'events' => [
            'plugin.extend.campaignbundle.subscriber' => [
                'class'     => MauticPlugin\ExtendCampaignBundle\EventListener\CampaignSubscriber::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                ],
            ],
        ],
    ],
];
