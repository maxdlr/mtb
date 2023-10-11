<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecurityManager extends AbstractController
{
    public function userIs(
        User $userToCheck,
    ): bool
    {
        return $this->getUser() === $userToCheck;
    }

    public function ownerOf(
        User $user,
        Post $post,
    ): bool
    {
        return in_array($user, (array)$post->getUser());
    }
}