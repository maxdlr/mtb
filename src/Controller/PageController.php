<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\EditPostType;
use App\Repository\UserRepository;
use App\Service\SecurityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;


class PageController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route('/{username}', name: 'app_user_page', methods: ['GET', 'POST'])]
    public function index(
        UserRepository         $userRepository,
        string                 $username,
        Request                $request,
        PostController         $postController,
        EntityManagerInterface $entityManager,
        SluggerInterface       $slugger,
        SecurityManager        $securityManager
    ): Response|FormView
    {
        $owner = $userRepository->findOneBy(['username' => $username]);
        $postForm = null;

        if (!$owner) {
            $this->addFlash('danger', 'Page inexistante');
            return $this->redirectToRoute('app_home');
        }

        if ($securityManager->UserIs($owner)) {
            $postForm = $postController->new(
                $request,
                $entityManager,
                $userRepository,
                $slugger
            );

            if ($postForm->isSubmitted() && $postForm->isValid()) {
                $entityManager->flush();
                return $this->redirectToRoute('app_user_page', ['username' => $owner->getUsername()], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('page/index.html.twig', [
            'owner' => $owner,
            'postForm' => $postForm,
        ]);
    }

    #[Route('/{username}/edit', name: 'app_user_editpage', methods: ['GET', 'POST'])]
    public function edit(
        UserRepository  $userRepository,
        string          $username,
        SecurityManager $securityManager,
    ): Response
    {
        $owner = $userRepository->findOneBy(['username' => $username]);

        if ($securityManager->userIs($owner)) {
            return $this->render('page/edit.html.twig', [
                'owner' => $owner,
            ]);
        } else {
            throw $this->createAccessDeniedException();
        }
    }

    #[Route('/{username}/{id}/edit', name: 'app_user_editpost', methods: ['GET', 'POST'])]
    #[ParamConverter('id', options: ['mapping' => ['id' => 'post_id']])]
    public function editPost(
        UserRepository         $userRepository,
        string                 $username,
        Request                $request,
        Post                   $post,
        EntityManagerInterface $entityManager,
        SecurityManager        $securityManager,
    )
    {
        $owner = $userRepository->findOneBy(['username' => $username]);

        $form = $this->createForm(EditPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_user_page', ['username' => $username], Response::HTTP_SEE_OTHER);
        }

        if ($securityManager->userIs($owner)) {
            return $this->render('page/_edit-post.html.twig', [
                'form' => $form
            ]);
        } else {
            throw $this->createAccessDeniedException();
        }
    }

}
