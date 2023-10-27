<?php

namespace App\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;

class DataManager
{
    public function isInFilteredArray(
        string $needleToFind,
        array  $inThisHaystack,
        string $filteredBy
    ): bool
    {
        $extractedFields = null;

        foreach ($inThisHaystack as $haystackField) {
            $extractedFields[] = $haystackField[$filteredBy];
        }

        return in_array($needleToFind, $extractedFields);
    }

    /**
     * @throws Exception
     */
    public function sortPostsByDayNumber(
        Collection $postCollection,
    ): Collection
    {
        $iterator = $postCollection->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getPrompt()->getDayNumber() < $b->getPrompt()->getDayNumber()) ? -1 : 1;
        });

        return new ArrayCollection(iterator_to_array($iterator));
    }
}