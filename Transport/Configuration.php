<?php

namespace MauticPlugin\UnosendSmsBundle\Transport;

use Mautic\PluginBundle\Helper\IntegrationHelper;

class Configuration
{
    private string $apiKey  = '';
    private string $apiUrl  = 'https://www.unosend.co';
    private string $from    = '';
    private bool $configured = false;

    public function __construct(
        private IntegrationHelper $integrationHelper,
    ) {
    }

    public function getApiKey(): string
    {
        $this->setConfiguration();

        return $this->apiKey;
    }

    public function getApiUrl(): string
    {
        $this->setConfiguration();

        return rtrim($this->apiUrl, '/');
    }

    public function getFrom(): string
    {
        $this->setConfiguration();

        return $this->from;
    }

    /**
     * @throws ConfigurationException
     */
    private function setConfiguration(): void
    {
        if ($this->configured) {
            return;
        }

        $integration = $this->integrationHelper->getIntegrationObject('UnosendSms');
        if (!$integration || !$integration->getIntegrationSettings()->getIsPublished()) {
            throw new ConfigurationException('Unosend SMS integration is not enabled');
        }

        $keys = $integration->getDecryptedApiKeys();
        if (empty($keys['password'])) {
            throw new ConfigurationException('Unosend API key is not configured');
        }

        $this->apiKey = $keys['password'];

        $features    = $integration->getIntegrationSettings()->getFeatureSettings();
        $this->apiUrl = !empty($features['api_url']) ? $features['api_url'] : 'https://www.unosend.co';
        $this->from   = $features['from'] ?? '';

        $this->configured = true;
    }
}
