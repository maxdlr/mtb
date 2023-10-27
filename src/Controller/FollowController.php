<?php

namespace App\Controller;

use App\Entity\Follow;
use App\Form\FollowType;
use App\Repository\UserRepository;
use App\Service\FollowManager;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\BadMessageException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FollowController extends AbstractController
{
    #[Route('/follow/{followerId}/{followedId}', name: 'app_user_follow')]
    public function followUser(
        int                    $followerId,
        int                    $followedId,
        Request                $request,
        UserRepository         $userRepository,
        EntityManagerInterface $entityManager,
        FollowManager          $followManager,
        FormFactoryInterface   $formFactory,
    ): JsonResponse
    {
        $follow = new Follow();
        $followerUser = $userRepository->findOneBy(['id' => $followerId]);
        $followedUser = $userRepository->findOneBy(['id' => $followedId]);

        if ($followManager->follow(
            $followerUser,
            $followedUser,
            $follow,
            $entityManager,
            $formFactory,
            $request
        )) {
            return $this->json(['message' => 'followed'], 200);
        } else {
            return $this->json(['message' => 'Not followed'], 200);
        }
    }
}