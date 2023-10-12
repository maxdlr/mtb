<?php

namespace App\Service;

use App\Entity\Follow;
use App\Entity\User;
use App\Form\FollowType;
use Cassandra\Type\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FollowManager
{
    public function follow(
        FormInterface          $form,
        User                   $follower,
        User                   $followed,
        Follow                 $follow,
        EntityManagerInterface $entityManager,
    ): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $follow->setFollower($follower);
            $follow->setFollowed($followed);
            $entityManager->persist($follow);
            $entityManager->flush();
            return true;
        } else {
            return false;
        }
    }

    public function unfollow(
        Follow $follow,
        User   $follower,
    ): bool
    {
        $follower->removeFollowing($follow);
        return true;
    }
}