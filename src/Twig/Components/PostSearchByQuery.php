<?php

namespace App\Twig\Components;

use App\Entity\User;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
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
        private readonly PostRepository  $postRepository,
        private readonly ReportComponent $reportComponent
    )
    {
    }

    #[LiveListener('selectedPostId')]
    public function openReportForm(#[LiveArg('post_id')] int $postId): void
    {
        $this->reportComponent->setPost($postId);
    }

    #[LiveListener('updatePosts')]
    public function getPosts(): array
    {
        $posts = [];

        if ($this->getOwner()) {
            $retrievedPosts = $this->postRepository->findByQueryByUser(
                $this->query,
                $this->getOwner(),
                100,
                $this->orderBy,
                $this->ascDesc
            );
        } else {
            $retrievedPosts = $this->postRepository->findByQuery(
                $this->query,
                100,
                $this->orderBy,
                $this->ascDesc
            );
        }

        foreach ($retrievedPosts as $post) {
            $posts[] = new ArrayCollection($post);
        }

        return $posts;
    }

    public function getOwner(): User|null
    {
        return $this->owner;
    }
}