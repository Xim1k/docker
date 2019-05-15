<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('old_pass', PasswordType::class,[
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Пожалуйста, введите старый пароль.',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Пароль должен быть длиннее {{ limit }} символов.',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                ]
                )
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Пароли должны совпадать',
                'required' => true,
                'first_options'  => ['label' => 'Новый пароль'],
                'second_options' => ['label' => 'Повторите пароль'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Пожалуйста, введите новый пароль.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Пароль должен быть длиннее {{ limit }} символов.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }
}
