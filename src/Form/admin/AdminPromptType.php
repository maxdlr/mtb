<?php

namespace App\Form\admin;

use App\Entity\Prompt;
use App\Repository\PromptRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminPromptType extends AbstractType
{
    public function __construct(private readonly PromptRepository $promptRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $prompt = $this->promptRepository->findOneById($options['object_id']);
        $promptLists = '';

        foreach ($prompt->getPromptList() as $list) {
            $promptLists .= $list->getYear();
            if (count($prompt->getPromptList()) > 1) $promptLists .= ', ';
        }

        $builder
            ->add('dayNumber', IntegerType::class, [
                'label' => false
            ])
            ->add('name_fr', TextType::class, [
                'label' => false
            ])
            ->add('name_en', TextType::class, [
                'label' => false
            ])// ->add('submit', SubmitType::class)
            ->add('liste', TextType::class, [
                'label' => false,
                'mapped' => false,
                'disabled' => true,
                'attr' => [
                    'placeholder' => $promptLists
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prompt::class,
            'object_id' => null
        ]);
    }
}
