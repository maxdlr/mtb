<?php

namespace App\Service;

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
}