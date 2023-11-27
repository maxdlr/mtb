<?php

namespace App\Form\admin;

use App\Entity\Page;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class AdminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', IntegerType::class, [
                'label' => false,
                'disabled' => true
            ])
            ->add('username', TextType::class, [
                'label' => false,
                'autocomplete' => false
            ])
            ->add('password', PasswordType::class, [
                'label' => false,
                'hash_property_path' => 'password',
                'mapped' => false,
            ])
            ->add('roles', ChoiceType::class, [
                'label' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'Mtber' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN'
                ],
                'empty_data' => 'ROLE_USER',
            ])
            ->add('registrationDate', DateTimeType::class, [
                'label' => false,
                'input' => 'datetime_immutable',
                'widget' => 'single_text'
            ])
            ->add('page', EntityType::class, [
                'label' => false,
                'class' => Page::class,
                'choice_label' => function (?Page $page) {
                    return $page->getId();
                }
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
