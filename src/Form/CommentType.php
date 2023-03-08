<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('grade', RangeType::class, [
                'label' => 'Note',
                'attr' => [
                    'min' => 0,
                    'max' => 5,
                    'value' => 3,
                ],
                'help' => 'Sélectionnez une note pour l\'article',
                'required' => true,
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => ['placeholder' => 'Titre de votre commentaire'],
                'required' => true,
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => ['placeholder' => 'Contenu de votre commentaire'],
                'required' => true,
            ])
            ->add('gdpr', CheckboxType::class, [
                'label' => 'Rgpd',
                'help' => 'En cochant cette case vous acceptez notre politique de confidentialité',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
