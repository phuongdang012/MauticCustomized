<?php

// plugins/HelloWorldBundle/Form/Type/ConfigType.php

namespace MauticPlugin\MauticFacebookBusinessBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ConfigType.
 */
class ConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'custom_config_option',
            'text',
            [
                'label' => 'plugin.facebook_business.config.custom_config_option',
                'data'  => $options['data']['custom_config_option'],
                'attr'  => [
                    'tooltip' => 'plugin.facebook_business.config.custom_config_option_tooltip',
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'facebook_business_config';
    }
}
