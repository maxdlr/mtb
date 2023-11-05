<?php

namespace App\Service;

use App\Entity\Post;
use App\Repository\PromptListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PromptListManager
{
    public function __construct(
        private readonly PromptListRepository $promptListRepository
    )
    {
    }

    public function getPostsPromptLists($posts): Collection
    {
        $promptLists = new ArrayCollection();

        foreach ($posts as $post) {
            $promptList = $this->promptListRepository->findOneBy(['year' => $post['promptListYear']]);

            if (!$promptLists->contains($promptList))
                $promptLists->add($promptList);
        }

        return $promptLists;
    }
}
