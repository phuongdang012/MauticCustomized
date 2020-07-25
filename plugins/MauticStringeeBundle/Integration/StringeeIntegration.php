<?php

namespace MauticPlugin\MauticStringeeBundle\Integration;

use Mautic\CoreBundle\Form\Type\YesNoButtonGroupType;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class StringeeIntegration extends AbstractIntegration
{
    public function getName()
    {
        return 'Stringee';
    }

    public function getIcon()
    {
        return 'plugins/MauticStringeeBundle/Assets/img/Stringee.png';
    }

    public function getRequiredKeyFields()
    {
        return [
            'sid'    => 'mautic.sms.config.form.stringee.sid',
            'secret' => 'mautic.sms.config.form.stringee.secret',
            'sender' => 'mautic.sms.config.form.stringee.sender',
        ];
    }

    public function getAuthenticationType()
    {
        return 'none';
    }

    public function getSupportedFeatures()
    {
        return [
            'sms',
        ];
    }

    public function appendToForm(&$builder, $data, $formArea)
    {
        if ('features' == $formArea) {
            $builder->add(
                'disable_trackable_urls',
                YesNoButtonGroupType::class,
                [
                    'label' => 'mautic.sms.config.form.stringee.disable_trackable_urls',
                    'attr'  => [
                        'tooltip' => 'mautic.sms.config.form.stringee.disable_trackable_urls.tooltip',
                    ],
                    'data' => !empty($data['disable_trackable_urls']) ? true : false,
                ]
            );

            $builder->add(
                'frequency_number',
                NumberType::class,
                [
                    'scale'      => 0,
                    'label'      => 'mautic.sms.list.frequency.stringee.number',
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
                    'label'      => 'mautic.lead.list.frequency.stringee.times',
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
