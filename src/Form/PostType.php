<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Prompt;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\UX\Dropzone\Form\DropzoneType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('post', DropzoneType::class, [
                'label' => "40mo max 😭",
                'attr' => [
                    'placeholder' => 'Drop it likes it\'s hot'
                ],

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional, so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                'multiple' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '40960k',
                        'mimeTypes' => [
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid img file (gif)',
                    ])
                ],
            ])
            ->add('prompt', PromptAutocompleteField::class, [
                'label' => false,
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
