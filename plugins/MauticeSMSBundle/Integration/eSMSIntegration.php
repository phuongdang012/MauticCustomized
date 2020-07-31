<?php

namespace MauticPlugin\MauticeSMSBundle\Integration;

use Mautic\CoreBundle\Form\Type\YesNoButtonGroupType;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class eSMSIntegration extends AbstractIntegration
{
    public function getName()
    {
        return 'eSMS';
    }

    public function getIcon()
    {
        return 'plugins/MauticeSMSBundle/Assets/img/esms.png';
    }

    public function getIdentifierFields()
    {
        return [
            'esms',
        ];
    }

    public function getRequiredKeyFields()
    {
        return [
            'api_key' => 'mautic.sms.config.form.esms.api_key',
            'secret'  => 'mautic.sms.config.form.esms.secret',
        ];
    }

    public function getAuthenticationType()
    {
        return 'none';
    }

    public function appendToForm(&$builder, $data, $formArea)
    {
        if ('features' == $formArea) {
            $builder->add(
                'sender',
                TextType::class,
                [
                    'label'      => 'mautic.sms.config.form.esms.sender',
                    'label_attr' => ['class' => 'control-label'],
                    'required'   => false,
                    'attr'       => [
                        'class'   => 'form-control',
                        'tooltip' => 'mautic.sms.config.form.esms.sender.tooltip',
                    ],
                ]
            );

            $builder->add(
                'disable_trackable_urls',
                YesNoButtonGroupType::class,
                [
                    'label' => 'mautic.sms.config.form.esms.disable_trackable_urls',
                    'attr'  => [
                        'tooltip' => 'mautic.sms.config.form.esms.disable_trackable_urls.tooltip',
                    ],
                    'data' => !empty($data['disable_trackable_urls']) ? true : false,
                ]
            );

            $builder->add(
                'frequency_number',
                NumberType::class,
                [
                    'scale'      => 0,
                    'label'      => 'mautic.sms.list.frequency.esms.number',
                    'label_attr' => ['class' => 'control-label'],
                    'required'   => false,
                    'attr'       => [
                        'class' => 'form-control frequency',
                    ],
                ]
            );

            $builder->add(
                'frequency_time',
                ChoiceType::class,
                [
                    'choices' => [
                        'day'   => 'ngày',
                        'week'  => 'tuần',
                        'month' => 'tháng',
                    ],
                    'label'      => 'mautic.lead.list.frequency.esms.times',
                    'label_attr' => ['class' => 'control-label'],
                    'required'   => false,
                    'multiple'   => false,
                    'attr'       => [
                        'class' => 'form-control frequency',
                    ],
                ]
            );
        }
    }
}
