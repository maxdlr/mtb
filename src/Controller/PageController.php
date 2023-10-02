<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;


class PageController extends AbstractController
{
    public function __construct(
    ){}

    #[Route('/{username}', name: 'app_user_page', methods: ['GET', 'POST'])]
    public function index(
        UserRepository $userRepository,
        PostRepository $postRepository,
        string $username,
        Request $request,
        PostController $postController,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
    ): Response | FormView
    {
        $owner = $userRepository->findOneBy(['username' => $username]);

        if (!$owner) {
            $this->addFlash('danger', 'Page inexistante');
            return $this->redirectToRoute('app_home');
        }

        $postForm = $postController->new(
            $request,
            $entityManager,
            $userRepository,
            $postRepository,
            $slugger
        );

//        $postForm = $postManager->createNewUserPostsForm($request);

        return $this->render('page/index.html.twig', [
            'owner' => $owner,
            'postForm' => $postForm,
        ]);
    }

}
