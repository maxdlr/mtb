<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecurityManager extends AbstractController
{
    public function userIs(
        User $owner,
    ): bool
    {
        $user = $this->getUser();

        if ($user && $user === $owner) {
            return true;
        } else {
            throw $this->createAccessDeniedException();
        }
    }

    public function isOwnerPost(
        Post $post
    ): bool
    {
        $user = $this->getUser();

        if ($user && $post->getUser()->get(0) !== $user) {
            throw $this->createAccessDeniedException();
        } else {
            return true;
        }
    }
}