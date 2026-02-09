<?php

namespace MauticPlugin\UnosendSmsBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UnosendSmsIntegration extends AbstractIntegration
{
    protected bool $coreIntegration = false;

    public function getName(): string
    {
        return 'UnosendSms';
    }

    public function getDisplayName(): string
    {
        return 'Unosend SMS';
    }

    public function getSecretKeys(): array
    {
        return ['password'];
    }

    public function getRequiredKeyFields(): array
    {
        return [
            'password' => 'unosend_sms.config.api_key',
        ];
    }

    public function getAuthenticationType(): string
    {
        return 'none';
    }

    public function appendToForm(&$builder, $data, $formArea): void
    {
        if ('features' !== $formArea) {
            return;
        }

        $builder->add(
            'from',
            TextType::class,
            [
                'label'    => 'unosend_sms.config.from',
                'required' => false,
                'data'     => $data['from'] ?? '',
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => '+18773987372 or Unosend',
                ],
            ]
        );

        $builder->add(
            'api_url',
            TextType::class,
            [
                'label'    => 'unosend_sms.config.api_url',
                'required' => false,
                'data'     => $data['api_url'] ?? 'https://www.unosend.co',
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => 'https://www.unosend.co',
                ],
            ]
        );
    }
}
