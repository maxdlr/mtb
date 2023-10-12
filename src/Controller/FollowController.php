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
        FollowManager          $followManager,
        EntityManagerInterface $entityManager,
    ): JsonResponse
    {
        $follower = $userRepository->findOneBy(['id' => $followerId]);
        $followed = $userRepository->findOneBy(['id' => $followedId]);

        $follow = new Follow();
        $form = $this->createForm(FollowType::class, $follow);
        $form->handleRequest($request);


        if ($followManager->follow($form, $follower, $followed, $follow, $entityManager)) {
            return $this->json(['message' => 'Enregistrement éffectué'], 200);
        } else {
            return $this->json(['message' => 'Follow raté !!!!']);
        }
    }
}