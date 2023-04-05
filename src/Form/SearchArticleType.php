<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Categorie;
use App\Search\SearchData;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('query', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Rechercher'
                ],
                'required' => false
            ])
            ->add('tags', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Categorie::class,
                'choice_label' => 'title',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->andWhere('c.enabled = true')
                        ->orderBy('c.title', 'ASC');
                },
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('authors', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->join('u.articles', 'a')
                        ->orderBy('u.lastName', 'ASC');
                },
                'choice_label' => 'fullName',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('enabled', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,
                'multiple' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false,
            'validation_groups' => false
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
