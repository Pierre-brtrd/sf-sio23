<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email:',
                'attr' => [
                    'placeholder' => 'john-doe@exemple.com'
                ],
                'required' => true,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Mot de passe:',
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'placeholder' => 'S3CR3T'
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez renseigner un mot de passe'
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                            'max' => 4096,
                            'maxMessage' => 'Votre mot de passe ne peux pas contenir plus de {{ limit }} caractères',
                        ])
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmation mot de passe',
                    'attr' => [
                        'placeholder' => 'Le même le mot de passe'
                    ]
                ],
                'invalid_message' => 'Les mot de passe doivent correspondre',
                'mapped' => false
            ])
            ->add('firstName', TextType::class, [
                'required' => true,
                'label' => 'Prénom:',
                'attr' => [
                    'placeholder' => 'John'
                ]
            ])
            ->add('lastName', TextType::class, [
                'required' => true,
                'label' => 'Nom:',
                'attr' => [
                    'placeholder' => 'Doe'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
