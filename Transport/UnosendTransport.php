<?php

namespace MauticPlugin\UnosendSmsBundle\Transport;

use Mautic\LeadBundle\Entity\Lead;
use Mautic\SmsBundle\Sms\TransportInterface;
use Psr\Log\LoggerInterface;

class UnosendTransport implements TransportInterface
{
    public function __construct(
        private Configuration $configuration,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Send an SMS via the Unosend API.
     *
     * @param string $content
     *
     * @return bool|string true on success, error message on failure
     */
    public function sendSms(Lead $lead, $content)
    {
        $number = $lead->getLeadPhoneNumber();
        if (empty($number)) {
            return 'mautic.sms.transport.error.no_phone';
        }

        $number = $this->normalizePhoneNumber($number);

        try {
            $apiUrl = $this->configuration->getApiUrl();
            $apiKey = $this->configuration->getApiKey();
            $from   = $this->configuration->getFrom();
        } catch (ConfigurationException $e) {
            $this->logger->warning('Unosend SMS not configured: '.$e->getMessage());

            return $e->getMessage();
        }

        $payload = [
            'to'   => $number,
            'body' => $content,
        ];

        if (!empty($from)) {
            $payload['from'] = $from;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $apiUrl.'/api/v1/sms',
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer '.$apiKey,
                'Content-Type: application/json',
                'Accept: application/json',
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if (!empty($error)) {
            $this->logger->error('Unosend SMS cURL error: '.$error);

            return 'Unosend API error: '.$error;
        }

        $data = json_decode($response, true);

        if (null === $data) {
            $this->logger->error('Unosend SMS invalid response: '.$response);

            return 'Unosend: invalid API response';
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            $errorMsg = $data['message'] ?? $data['error'] ?? 'Unknown error';
            $this->logger->warning('Unosend SMS HTTP error: '.$errorMsg, [
                'phone'    => $number,
                'httpCode' => $httpCode,
                'response' => $response,
            ]);

            return 'Unosend: '.$errorMsg;
        }

        $results = $data['data'] ?? [];
        if (empty($results)) {
            $this->logger->warning('Unosend SMS: empty response data', [
                'phone'    => $number,
                'response' => $response,
            ]);

            return 'Unosend: no send result returned';
        }

        $result = $results[0];
        if ('failed' === ($result['status'] ?? '')) {
            $errorMsg = $result['error'] ?? 'Send failed';
            $this->logger->warning('Unosend SMS send failed: '.$errorMsg, [
                'phone'    => $number,
                'response' => $response,
            ]);

            return 'Unosend: '.$errorMsg;
        }

        $this->logger->info('Unosend SMS sent successfully', [
            'phone'     => $number,
            'messageId' => $result['id'] ?? null,
            'status'    => $result['status'] ?? null,
            'segments'  => $result['segments'] ?? null,
        ]);

        return true;
    }

    /**
     * Normalize phone number to E.164 format.
     */
    private function normalizePhoneNumber(string $number): string
    {
        $number = preg_replace('/[\s\-\(\)]/', '', $number);

        if (str_starts_with($number, '+')) {
            return $number;
        }

        // Dutch local format: 06xxxxxxxx -> +316xxxxxxxx
        if (str_starts_with($number, '0')) {
            return '+31'.substr($number, 1);
        }

        return '+'.$number;
    }
}
