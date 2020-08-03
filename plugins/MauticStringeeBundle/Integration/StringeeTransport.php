<?php

namespace MauticPlugin\MauticStringeeBundle\Integration;

use Exception;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\SmsBundle\Sms\TransportInterface;
use MauticPlugin\MauticStringeeBundle\Integration\SDK\Rest\Rest;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber;
use Psr\Log\LoggerInterface;

class StringeeTransport implements TransportInterface
{
    private $config;
    private $restClient;
    private $logger;

    public function __construct(StringeeConfiguration $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    public function sendSms(Lead $lead, $smsBody)
    {
        $number = $lead->getLeadPhoneNumber();

        if (null === $number) {
            return false;
        }
        try {
            $this->configureClient();
            $this->restClient->create(
                $this->sanitizeNumber($number),
                $smsBody
            );
        } catch (NumberParseException $ex) {
            $this->logger->warning(
                $ex->getMessage(),
                ['exception' => $ex]
            );
        } catch (Exception $ex) {
            $message = ($ex->getMessage()) ? $ex->getMessage() : 'mautic.sms.stringee.transport.not_configured';
            $this->logger->warning(
                $message,
                ['exception' => $ex]
            );

            return $message;
        } catch (Exception $ex) {
            $this->logger->warning(
                $ex->getMessage(),
                ['exception' => $ex]
            );

            return $ex->getMessage();
        }
    }

    public function sanitizeNumber($number)
    {
        $utils        = PhoneNumberUtil::getInstance();
        $parsedNumber = $utils->parse($number, 'VN');

        return ltrim($utils->format($parsedNumber, PhoneNumberFormat::E164), '+');
    }

    public function configureClient()
    {
        if ($this->restClient) {
            return;
        }

        $this->restClient = new Rest(
            $this->config->getSid(),
            $this->config->getSecret(),
            $this->config->getSender()
        );
    }
}
