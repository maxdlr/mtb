<?php

namespace App\Components;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class PostSearchByQuery
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?string $query = null;
    public Collection $posts;
    public string $orderBy = 'prompt.dayNumber';
    public string $ascDesc = 'ASC';

    public function __construct(
        private readonly PostRepository $postRepository,
    )
    {
    }

    public function getPosts(): array
    {
        return $this->postRepository->findByQuery(
            $this->query,
            100,
            $this->orderBy,
            $this->ascDesc
        );
    }
}