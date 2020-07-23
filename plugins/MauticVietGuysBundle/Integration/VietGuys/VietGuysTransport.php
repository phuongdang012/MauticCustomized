<?php

namespace MauticPlugin\MauticVietGuysBundle\Integration\VietGuys;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use Mautic\SmsBundle\Controller\Api\SmsApiController;
use Mautic\SmsBundle\Sms\TransportInterface;
use Psr\Log\LoggerInterface;
use VietGuys\Exception\VietGuysException;
use VietGuys\Rest\VietGuysRestClient;

class VietGuysTransport implements TransportInterface
{
    private $configuration;
    private $restClient;
    private $logger;

    public function __construct(VietGuysConfiguration $configuration, LoggerInterface $logger)
    {
        $this->logger        = $logger;
        $this->configuration = $configuration;
    }

    public function sendSms(Lead $lead, $content)
    {
        $number = $lead->getLeadPhoneNumber();

        if (null === $number) {
            return false;
        }
        try {
            $this->configureClient();
            $this->restClient->create(
                com_create_guid(),
                $this->sanitizeNumber($number),
                $content
            );
            $this->logger->debug(
                'VietGuys text message sent!',
                [
                    'content' => $content,
                ]
            );
        } catch (NumberParseException $ex) {
            $this->logger->warning(
                $ex->getMessage(),
                ['exception' => $ex]
            );

            return $ex->getMessage();
        } catch (VietGuysException $ex) {
            $message = ($ex->getMessage()) ? $ex->getMessage() : 'mautic.sms.vietguys.transport.not_configured';
            $this->logger->warning(
                $message,
                ['exception' => $ex]
            );

            return $message;
        } catch (VietGuysException $ex) {
            $this->logger->warning(
                $ex->getMessage(),
                ['exception' => $ex]
            );

            return $ex->getMessage();
        }
    }

    private function sanitizeNumber($number)
    {
        $util   = PhoneNumberUtil::getInstance();
        $parsed = $util->parse($number, 'VN');

        return $util->format($parsed, PhoneNumberFormat::E164);
    }

    public function configureClient()
    {
        if ($this->restClient) {
            return;
        }

        $this->restClient = new VietGuysRestClient(
            $this->configuration->getUsername(),
            $this->configuration->getPassword(),
            $this->configuration->getSender()
        );
    }
}
