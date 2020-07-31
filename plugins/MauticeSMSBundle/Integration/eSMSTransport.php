<?php

namespace MauticPlugin\MauticeSMSBundle\Integration;

use Exception;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\SmsBundle\Sms\TransportInterface;
use MauticPlugin\MauticeSMSBundle\Integration\SDK\eSMSClient;
use Psr\Log\LoggerInterface;

class eSMSTransport implements TransportInterface
{
    private $config;
    private $client;
    private $logger;

    public function __construct(eSMSConfiguration $config, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->config = $config;
    }

    public function sendSms(Lead $lead, $content)
    {
        $number = $lead->getLeadPhoneNumber();

        if (null === $number) {
            return false;
        }
        try {
            $this->configureClient();
            $response = $this->client->create($number, $content);
            switch ($response->getCodeResult()) {
                case 100:
                    $this->logger->info('eSMS text message sent!!');
                    break;
                default:
                    $this->logger->error($response->translateCodeToError());
                    break;
            }
        } catch (NumberParseException $ex) {
            $this->logger->warning(
                $ex->getMessage(),
                ['exception' => $ex]
            );

            return $ex->getMessage();
        } catch (Exception $ex) {
            $message = ($ex->getMessage()) ? $ex->getMessage() : 'mautic.sms.esms.transport.not_configured';
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

    public function configureClient()
    {
        if ($this->client) {
            return;
        }

        $this->client = new eSMSClient(
            $this->config->getApiKey(),
            $this->config->getSecret(),
            $this->config->getSenderType(),
            $this->config->getSender()
        );
    }
}
