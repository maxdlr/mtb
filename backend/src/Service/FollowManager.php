<?php

namespace App\Service;

use App\Entity\Follow;
use App\Entity\User;
use App\Form\FollowType;
use App\Repository\FollowRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FollowManager
{
    public function createFollowForm(
        FormFactoryInterface $formFactory,
        User                 $followedUser,
        Follow               $follow
    ): FormInterface
    {
        return $formFactory->createNamed('follow_' . $followedUser->getId(), FollowType::class, $follow);
    }

    public function follow(
        User                   $followerUser,
        User                   $followedUser,
        Follow                 $follow,
        EntityManagerInterface $entityManager,
        FormFactoryInterface   $formFactory,
        Request                $request,
    ): bool
    {
        $form = $this->createFollowForm($formFactory, $followedUser, $follow);
        $form->handleRequest($request);

//        if ($form->isSubmitted() && $form->isValid()) {
        $follow->setFollower($followerUser);
        $follow->setFollowed($followedUser);
        $entityManager->persist($follow);
        $entityManager->flush();
        return true;
//        } else {
//            return false;
//        }
    }

    public function unfollow(
        Follow $follow,
        User   $follower,
    ): bool
    {
        $follower->removeFollowing($follow);
        return true;
    }

    public function IsFollowing(
        User             $followerUser,
        User             $followedUser,
        FollowRepository $followRepository,
        UserRepository   $userRepository,
    ): bool
    {
        $ownerFollowers = [];
        $ownerFollowerIds = $followRepository->findFollowersOf($followedUser->getId());

        foreach ($ownerFollowerIds as $ownerFollowerId) {
            $ownerFollowers[] = $userRepository->findBy(['id' => $ownerFollowerId])[0];
        }

        $ownerFollowers = new ArrayCollection(iterator_to_array($ownerFollowers));

        return $ownerFollowers->contains($followerUser);
    }
}