<?php

namespace App\Form;

use App\Entity\Prompt;
use App\Entity\Violation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class ViolationAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Violation::class,
            'placeholder' => 'Choisi une raison',
            'choice_label' => function (?Violation $violation) {
                return $violation->getName() . ' - ' . $violation->getDescription();
            },
            'no_results_found_text' => 'Aucun résultat',
            'no_more_results_text' => 'Y a plus rien.',
            'searchable_fields' => ['name'],
            'multiple' => false,
            'row_attr' => ['class' => 'py-0 px-2 m-0'],
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
