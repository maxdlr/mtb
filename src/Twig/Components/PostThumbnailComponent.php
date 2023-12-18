<?php

namespace App\Twig\Components;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent()]
final class PostThumbnailComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    public ?ArrayCollection $post = null;
    public bool $showOwner = true;
    public bool $showPrompt = true;
    public bool $allowCopy = true;
    public bool $allowReport = true;

    public function __construct(private readonly PostRepository $postRepository)
    {
    }

    #[LiveAction]
    public function emitPostId(#[LiveArg] int $id): void
    {
        $this->post = $this->postRepository->findOneById($id);
        $this->emit('selectedPostId', [
            'post_id' => $id,
        ]);
    }
}
