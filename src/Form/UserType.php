<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();

            if ($user === $this->security->getUser()) {
                $form
                    ->add('email', EmailType::class, [
                        'label' => 'Email:',
                        'attr' => [
                            'placeholder' => 'john-doe@exemple.com'
                        ],
                        'required' => true,
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

            if ($this->security->isGranted('ROLE_ADMIN')) {
                $form->add('roles', ChoiceType::class, [
                    'choices' => [
                        'Utilisateur' => 'ROLE_USER',
                        'Editeur' => 'ROLE_EDITOR',
                        'Administrateur' => 'ROLE_ADMIN'
                    ],
                    'label' => 'Roles:',
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true,
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
