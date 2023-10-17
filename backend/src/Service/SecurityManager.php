<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;

class SecurityManager
{
    public function userIsOwner(
        User $thisUser,
        User $owner,
    ): bool
    {
        return $thisUser === $owner;
    }

    public function isOwnerOfPost(
        User $user,
        Post $post,
    ): bool
    {
        return in_array($user, (array)$post->getUser());
    }
}