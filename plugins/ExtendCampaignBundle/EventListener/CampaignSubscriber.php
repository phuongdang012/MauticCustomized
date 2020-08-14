<?php

namespace MauticPlugin\ExtendCampaignBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Event\CampaignBuilderEvent;
use Mautic\CampaignBundle\Event\CampaignEvent;
use Mautic\CampaignBundle\Event\CampaignExecutionEvent;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\ExtendCampaignBundle\ExtendCampaignEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CampaignSubscriber implements EventSubscriberInterface
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD        => ['onCampaignBuild', 0],
            ExtendCampaignEvents::VALIDATE_EXISTANCE => ['validateCampaignCondition', 0],
        ];
    }

    public function onCampaignBuild(CampaignBuilderEvent $event)
    {
        $event->addCondition(
            'lead.existance',
            [
                'eventName'       => ExtendCampaignEvents::VALIDATE_EXISTANCE,
                'label'           => 'plugin.extend_campaign_bundle.lead_existance',
                'description'     => 'plugin.extend_campaign_bundle.lead_existance_desc',
                'formType'        => false,
                'formTypeOptions' => [],
            ]
        );
    }

    public function validateCampaignCondition(CampaignExecutionEvent $event)
    {
        $lead    = $event->getLead();
        $dbQuery = $this->entityManager->getConnection()->createQueryBuilder()
            ->select('l.id')
            ->from(MAUTIC_TABLE_PREFIX.'leads', '1')
            ->where('email = :email')
            ->orWhere('phone = :phone')
            ->setParameters([
                'email' => $lead->getEmail(),
                'phone' => $lead->getPhone(),
            ]);
        $result = $dbQuery->execute()->fetchAll();
        if (count($result) <= 0) {
            $event->setResult(false);
        } else {
            $event->setResult(true);
        }
    }
}
