<?php

return [
    'name'        => 'Unosend SMS',
    'description' => 'SMS transport for Unosend API (unosend.co)',
    'version'     => '1.0.0',
    'author'      => 'Radata',

    'services' => [
        'integrations' => [
            'mautic.integration.unosendsms' => [
                'class' => \MauticPlugin\UnosendSmsBundle\Integration\UnosendSmsIntegration::class,
            ],
        ],
        'others' => [
            'mautic.sms.transport.unosend.configuration' => [
                'class'     => \MauticPlugin\UnosendSmsBundle\Transport\Configuration::class,
                'arguments' => [
                    'mautic.helper.integration',
                ],
            ],
            'mautic.sms.transport.unosend' => [
                'class'     => \MauticPlugin\UnosendSmsBundle\Transport\UnosendTransport::class,
                'arguments' => [
                    'mautic.sms.transport.unosend.configuration',
                    'monolog.logger.mautic',
                ],
                'tag'          => 'mautic.sms_transport',
                'tagArguments' => [
                    'channel'          => 'UnosendSms',
                    'integrationAlias' => 'UnosendSms',
                ],
            ],
        ],
    ],
];
