<?php

namespace App\Twig\Components;

use App\Entity\User;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class PostSearchByQuery
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?string $query = '';
    public Collection $posts;
    public string $orderBy = 'prompt.dayNumber';
    public string $ascDesc = 'ASC';

    #[LiveProp]
    public ?User $owner = null;

    public function __construct(
        private readonly PostRepository $postRepository,
    )
    {
    }

    #[LiveListener('postAdded')]
    public function getPosts(): array
    {
        if ($this->getOwner())
            return $this->postRepository->findByQueryByUser(
                $this->query,
                $this->getOwner(),
                100,
                $this->orderBy,
                $this->ascDesc
            );

        return $this->postRepository->findByQuery(
            $this->query,
            100,
            $this->orderBy,
            $this->ascDesc
        );
    }

    public function getOwner(): User|null
    {
        return $this->owner;
    }
}