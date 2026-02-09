<?php

namespace MauticPlugin\UnosendSmsBundle\Form\Type;

use Mautic\CoreBundle\Form\Type\FormButtonsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SendSmsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'contactId',
            HiddenType::class,
            [
                'data' => $options['data']['contactId'] ?? '',
            ]
        );

        $builder->add(
            'smsId',
            ChoiceType::class,
            [
                'label'       => 'unosend_sms.send.select_sms',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control'],
                'choices'     => array_flip($options['sms_choices']),
                'placeholder' => 'unosend_sms.send.select_placeholder',
                'required'    => true,
                'constraints' => [
                    new NotBlank(['message' => 'unosend_sms.send.error.no_sms']),
                ],
            ]
        );

        $builder->add(
            'buttons',
            FormButtonsType::class,
            [
                'apply_text'     => false,
                'save_text'      => 'unosend_sms.send.submit',
                'cancel_onclick' => 'javascript:void(0);',
                'cancel_attr'    => [
                    'data-dismiss' => 'modal',
                ],
            ]
        );

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'sms_choices' => [],
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'unosend_send_sms';
    }
}
