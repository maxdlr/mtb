<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\PostManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;


class PageController extends AbstractController
{
    public function __construct(
        PostManager $postManager
    ){}

    #[Route('/{username}', name: 'app_user_page', methods: ['GET', 'POST'])]
    public function index(
        UserRepository $userRepository,
        string $username,
        PostManager $postManager,
        Request $request,
    ): Response | FormView
    {
        $owner = $userRepository->findOneBy(['username' => $username]);

        if (!$owner) {
            $this->addFlash('danger', 'Page inexistante');
            return $this->redirectToRoute('app_home');
        }

        $postForm = $postManager->createNewUserPostsForm($request);

        return $this->render('page/index.html.twig', [
            'owner' => $owner,
            'postForm' => $postForm,
        ]);
    }
}
