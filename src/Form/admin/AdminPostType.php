<?php

namespace App\Form\admin;

use App\Entity\Post;
use App\Form\autocomplete\PromptAutocompleteField;
use App\Form\autocomplete\UserAutocompleteField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', UserAutocompleteField::class, [
                'label' => false,
            ])
            ->add('prompt', PromptAutocompleteField::class, [
                'label' => false,
            ])
            ->add('fileName', TextType::class)
            ->add('fileSize', IntegerType::class)
            ->add('uploadedOn', DateTimeType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
