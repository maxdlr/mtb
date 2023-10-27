<?php

namespace App\Form;

use App\Entity\Follow;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FollowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('follower', IntegerType::class, [
                'row_attr' => [
                    'class' => 'd-none',
                ],
                'label' => false,
                'attr' => [
                    'hidden' => 'hidden',
                    'data-follow-target' => 'follower'
                ]
            ])
            ->add('followed', IntegerType::class, [
                'row_attr' => [
                    'class' => 'd-none',
                ],
                'label' => false,
                'attr' => [
                    'hidden' => 'hidden',
                    'data-follow-target' => 'followed'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'data-action' => 'follow#followUser',
                    'data-follow-target' => 'button'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Follow::class,
        ]);
    }
}
