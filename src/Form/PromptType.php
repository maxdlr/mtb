<?php

namespace App\Form;

use App\Entity\Prompt;
use App\Entity\PromptList;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
                'label' => false
            ])
            ->add('name_fr', TextType::class, [
                'label' => false
            ])
            ->add('name_en', TextType::class, [
                'label' => false
            ])
            ->add('prompt_list', EntityType::class, [
                'label' => false,
                'class' => PromptList::class,
                'choice_label' => 'year',
                'multiple' => true,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prompt::class,
        ]);
    }
}
