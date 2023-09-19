<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/{username}', name: 'app_user_page')]
    public function index(
        UserRepository $userRepository,
        string $username
    ): Response
    {
        $user = $userRepository->findOneBy(['username' => $username]);

        if (!$user) {
            $this->addFlash('danger', 'Page inexistante');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('page/index.html.twig', [
            'user' => $user,
        ]);
    }
}
