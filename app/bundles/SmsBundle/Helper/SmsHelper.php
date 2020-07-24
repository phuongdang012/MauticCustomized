<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\SmsBundle\Helper;

use Doctrine\ORM\EntityManager;
use Exception;
use libphonenumber\PhoneNumberFormat;
use Mautic\CoreBundle\Helper\PhoneNumberHelper;
use Mautic\LeadBundle\Entity\DoNotContact as DoNotContactEntity;
use Mautic\LeadBundle\Entity\LeadRepository;
use Mautic\LeadBundle\Model\DoNotContact;
use Mautic\LeadBundle\Model\LeadModel;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use Mautic\SmsBundle\Model\SmsModel;
use Mautic\SmsBundle\Sms\TransportInterface;

class SmsHelper
{
    protected $em;

    protected $leadModel;

    protected $phoneNumberHelper;

    protected $smsModel;

    protected $integrationHelper;

    private $doNotContact;

    private $primaryTransport;

    private $transports;

    public function __construct(
        EntityManager $em,
        LeadModel $leadModel,
        PhoneNumberHelper $phoneNumberHelper,
        SmsModel $smsModel,
        IntegrationHelper $integrationHelper,
        DoNotContact $doNotContact,
        $primaryTransport
    ) {
        $this->em                   = $em;
        $this->leadModel            = $leadModel;
        $this->phoneNumberHelper    = $phoneNumberHelper;
        $this->smsModel             = $smsModel;
        $this->integrationHelper    = $integrationHelper;
        $this->doNotContact         = $doNotContact;
        $this->primaryTransport     = $primaryTransport;
    }

    public function addTransport($alias, TransportInterface $transport, $translatableAlias, $integrationAlias)
    {
        $this->transports[$alias]['alias']            = $translatableAlias;
        $this->transports[$alias]['integrationAlias'] = $integrationAlias;
        $this->transports[$alias]['service']          = $transport;

        return $this;
    }

    private function getPrimarySmsIntegration()
    {
        $enabled = [];

        foreach ($this->transports as $alias => $transport) {
            if (!isset($transport['published'])) {
                $integration = $this->integrationHelper->getIntegrationObject($transport['integrationAlias']);
                if (!$integration) {
                    continue;
                }
                $transport['published']   = $integration->getIntegrationSettings()->getIsPublished();
            }
            if ($transport['published']) {
                $enabled[$alias] = $integration;
            }
        }

        // If there no primary transport selected and there is just one available we will use it as primary
        if (1 === count($enabled)) {
            return array_shift($enabled);
        }

        if (0 === count($enabled)) {
            throw new Exception('Primary SMS transport is not enabled');
        }

        if (!array_key_exists($this->primaryTransport, $enabled)) {
            throw new Exception('Primary SMS transport is not enabled. '.$this->primaryTransport);
        }

        return $enabled[$this->primaryTransport];
    }

    public function unsubscribe($number)
    {
        $number = $this->phoneNumberHelper->format($number, PhoneNumberFormat::E164);

        /** @var LeadRepository $repo */
        $repo = $this->em->getRepository('MauticLeadBundle:Lead');

        $args = [
            'filter' => [
                'force' => [
                    [
                        'column' => 'mobile',
                        'expr'   => 'eq',
                        'value'  => $number,
                    ],
                ],
            ],
        ];

        $leads = $repo->getEntities($args);

        if (!empty($leads)) {
            $lead = array_shift($leads);
        } else {
            // Try to find the lead based on the given phone number
            $args['filter']['force'][0]['column'] = 'phone';

            $leads = $repo->getEntities($args);

            if (!empty($leads)) {
                $lead = array_shift($leads);
            } else {
                return false;
            }
        }

        return $this->doNotContact->addDncForContact($lead->getId(), 'sms', null, DoNotContactEntity::UNSUBSCRIBED);
    }

    /**
     * @return bool
     */
    public function getDisableTrackableUrls()
    {
        $integration = $this->getPrimarySmsIntegration();

        if (!is_null($integration)) {
            $settings = $integration->getIntegrationSettings()->getFeatureSettings();

            return !empty($settings['disable_trackable_urls']) ? true : false;
        }

        return false;
    }
}
