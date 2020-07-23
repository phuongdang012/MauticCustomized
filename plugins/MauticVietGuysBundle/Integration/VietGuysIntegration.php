<?php

namespace MauticPlugin\MauticVietGuysBundle\Integration;

use Mautic\CoreBundle\Form\Type\YesNoButtonGroupType;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class VietGuysIntegration extends AbstractIntegration
{
    public function getName()
    {
        return 'VietGuys';
    }

    public function getIcon()
    {
        return 'plugins/MauticVietGuysBundle/Assets/img/VietGuys.png';
    }

    public function getIdentifierFields()
    {
        return [
            'vietguys',
        ];
    }

    public function getRequiredKeyFields()
    {
        return [
            'username' => 'mautic.sms.config.form.vietguys.username',
            'password' => 'mautic.sms.config.form.vietguys.password',
            'sender'   => 'mautic.sms.config.form.vietguys.sender',
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
                'disable_trackable_urls',
                YesNoButtonGroupType::class,
                [
                    'label' => 'mautic.sms.config.form.vietguys.disable_trackable_urls',
                    'attr'  => [
                        'tooltip' => 'mautic.sms.config.form.vietguys.disable_trackable_urls.tooltip',
                    ],
                    'data' => !empty($data['disable_trackable_urls']) ? true : false,
                ]
            );

            $builder->add(
                'frequency_number',
                NumberType::class,
                [
                    'scale'      => 0,
                    'label'      => 'mautic.sms.list.frequency.vietguys.number',
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
                    'label'      => 'mautic.lead.list.frequency.vietguys.times',
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
