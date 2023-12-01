<?php

namespace App\Twig\Components;

use App\Entity\Post;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent()]
final class PostThumbnailComponent
{
    use DefaultActionTrait;

    public ArrayCollection $post;
    public bool $rich = true;
}
