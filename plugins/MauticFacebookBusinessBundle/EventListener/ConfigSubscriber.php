<?php

namespace MauticPlugin\MauticFacebookBusinessBundle\EventListener;

use Mautic\ConfigBundle\ConfigEvents;
use Mautic\ConfigBundle\Event\ConfigBuilderEvent;
use Mautic\ConfigBundle\Event\ConfigEvent;
use MauticPlugin\MauticFacebookBusinessBundle\Form\Type\ConfigType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ConfigEvents::CONFIG_ON_GENERATE => ['onConfigGenerate', 0],
            ConfigEvents::CONFIG_PRE_SAVE    => ['onConfigSave', 0],
        ];
    }

    public function onConfigGenerate(ConfigBuilderEvent $event)
    {
        $event->addForm(
            [
                'formAlias'  => 'facebook_business_config',
                'formTheme'  => 'MauticFacebookBusinessBundle:Config\Config',
                'formType'   => ConfigType::class,
                'parameters' => $event->getParametersFromConfig('MauticFacebookBusinessBundle'),
            ]
        );
    }

    public function onConfigSave(ConfigEvent $event)
    {
        /** @var array $values */
        $values = $event->getConfig();

        // Manipulate the values
        if (!empty($values['facebook_business_config']['custom_config_option'])) {
            $values['facebook_business_config']['custom_config_option'] = htmlspecialchars($values['facebook_business_config']['custom_config_option']);
        }

        // Set updated values
        $event->setConfig($values);
    }
}
