<?php

namespace App\Form;

use App\Entity\Prompt;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class PromptAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Prompt::class,
            'placeholder' => 'Tape le nom ou le numero d\'un thème',
            'choice_label' => function (?Prompt $prompt) {
                return $prompt->getDayNumber() . ' - ' . $prompt->getNameFr() . ' / ' . $prompt->getNameEn();
            },
            'no_results_found_text' => 'On a rien trouvé !',
            'no_more_results_text' => 'Y a plus rien !',
            'searchable_fields' => ['name_fr', 'name_en', 'dayNumber'],
            'multiple' => false
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
