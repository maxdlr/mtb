<?php

namespace App\Form;

use App\Entity\Participant;
use phpDocumentor\Reflection\PseudoTypes\NonEmptyList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Prénom'
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nom'
                ]
            ])
            ->add('instagram', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Lien Instagram'
                ]
            ])
            ->add('discord', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Lien Profil Discord'
                ]
            ])
            ->add('website', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Lien Site perso/portfolio'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
