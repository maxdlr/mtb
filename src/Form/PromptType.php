<?php

namespace App\Form;

use App\Entity\Prompt;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromptType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dayNumber', IntegerType::class, [
                'label' => 'Jour du mois',
                'attr' => [
                    'placeholder' => '31'
                ]
            ])
            ->add('name_fr', TextType::class, [
                'label' => 'Thème FR',
                'attr' => [
                    'placeholder' => 'Gros cul thème'
                ]
            ])
            ->add('name_en', TextType::class, [
                'label' => 'Thème EN',
                'attr' => [
                    'placeholder' => 'Big ass theme'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prompt::class,
        ]);
    }
}
