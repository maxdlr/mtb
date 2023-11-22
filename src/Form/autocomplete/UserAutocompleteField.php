<?php

namespace App\Form\autocomplete;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class UserAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => User::class,
            'placeholder' => 'Tape le nom d\'un utilisateur',
            'choice_label' => function (?User $user) {
                return $user->getUsername();
            },
            'no_results_found_text' => 'Aucun utilisateur trouvé !',
            'no_more_results_text' => 'Y en a plus !',
            'searchable_fields' => ['username'],
            'multiple' => true,
            'row_attr' => ['class' => 'py-0 px-2 m-0'],
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
