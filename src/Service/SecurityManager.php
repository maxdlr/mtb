<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SecurityManager
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

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
        return $post->getUser()->contains($user);
    }

    public function doesUsernameExist(string $username): bool
    {
        $allUsers = $this->userRepository->findAll();
        $usernames = [];

        foreach ($allUsers as $user) {
            $usernames[] = $user->getUsername();
        }

        return in_array($username, $usernames);


    }
}