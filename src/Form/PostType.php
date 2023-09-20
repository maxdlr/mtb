<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Prompt;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageFile', DropzoneType::class)
            ->add('prompt', EntityType::class, [
                'class' => Prompt::class,
                'choice_label' => 'name_fr',

                // used to render a select box, check boxes or radios
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('submit', SubmitType::class)
//            ->getForm()
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
