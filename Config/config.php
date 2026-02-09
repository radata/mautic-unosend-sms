<?php

return [
    'name'        => 'Unosend SMS',
    'description' => 'SMS transport for Unosend API (unosend.co)',
    'version'     => '1.0.0',
    'author'      => 'Radata',

    'routes' => [
        'main' => [
            'mautic_plugin_unosendsms_action' => [
                'path'       => '/unosendsms/{objectAction}/{objectId}',
                'controller' => 'MauticPlugin\UnosendSmsBundle\Controller\SmsController::executeAction',
            ],
        ],
    ],

    'services' => [
        'integrations' => [
            'mautic.integration.unosendsms' => [
                'class'     => \MauticPlugin\UnosendSmsBundle\Integration\UnosendSmsIntegration::class,
                'arguments' => [
                    'event_dispatcher',
                    'mautic.helper.cache_storage',
                    'doctrine.orm.entity_manager',
                    'request_stack',
                    'router',
                    'translator',
                    'monolog.logger.mautic',
                    'mautic.helper.encryption',
                    'mautic.lead.model.lead',
                    'mautic.lead.model.company',
                    'mautic.helper.paths',
                    'mautic.core.model.notification',
                    'mautic.lead.model.field',
                    'mautic.plugin.model.integration_entity',
                    'mautic.lead.model.dnc',
                    'mautic.lead.field.fields_with_unique_identifier',
                ],
            ],
        ],
        'events' => [
            'mautic.unosendsms.subscriber.buttons' => [
                'class'     => \MauticPlugin\UnosendSmsBundle\EventListener\ButtonSubscriber::class,
                'arguments' => [
                    'mautic.helper.integration',
                    'translator',
                    'router',
                ],
            ],
        ],
        'forms' => [
            'mautic.form.type.unosend_send_sms' => [
                'class' => \MauticPlugin\UnosendSmsBundle\Form\Type\SendSmsType::class,
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
