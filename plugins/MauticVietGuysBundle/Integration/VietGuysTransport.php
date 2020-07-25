<?php

namespace MauticPlugin\MauticVietGuysBundle\Integration;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\SmsBundle\Sms\TransportInterface;
use MauticPlugin\MauticVietGuysBundle\Integration\VietGuys\SDK\Exceptions\VietGuysException;
use MauticPlugin\MauticVietGuysBundle\Integration\VietGuys\SDK\Rest\RestClient;
use Psr\Log\LoggerInterface;

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
            $response = $this->restClient->create(
                $this->generateGuid(),
                $this->sanitizeNumber($number),
                $content
            );
            $this->logger->debug(
                'VietGuys text message sent!',
                [
                    'content' => $response,
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

        $this->restClient = new RestClient(
            $this->configuration->getUsername(),
            $this->configuration->getPassword(),
            $this->configuration->getSender()
        );
    }

    private function generateGuid()
    {
        if (true === function_exists('com_create_guid')) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
}
