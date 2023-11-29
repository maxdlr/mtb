<?php

namespace App\Form\admin;

use App\Entity\Post;
use App\Form\autocomplete\PromptAutocompleteField;
use App\Form\autocomplete\UserAutocompleteField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //todo: add post edit feat when click on picture
            ->add('id', IntegerType::class, [
                'label' => false,
                'disabled' => true
            ])
            ->add('user', UserAutocompleteField::class, [
                'label' => false,
            ])
            ->add('prompt', PromptAutocompleteField::class, [
                'label' => false,
            ])
            ->add('fileName', TextType::class, [
                'label' => false,
                'disabled' => true
            ])
            ->add('fileSize', IntegerType::class, [
                'label' => false,
                'disabled' => true,
            ])
            ->add('uploadedOn', DateTimeType::class, [
                'label' => false,
                'input' => 'datetime_immutable',
                'widget' => 'single_text'
            ])// ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
