<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Entity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostManager extends AbstractController
{
    public EntityManagerInterface $entityManager;
    public UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
    ){
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;

    }

    public function createNewUserPostsForm(
        Request $request,
    ): FormView | null
    {
        if ($this->getUser()) {

        $post = new Post();
        $now = new DateTimeImmutable();
        $user = $this->userRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        $post->setPostDate($now);
        $post->addUser($user);

        $this->processNewUserPostForm($form, $user, $post);

        return $form->createView();
        }

        return null;
    }

    public function processNewUserPostForm(
        FormInterface $form,
        User $user,
        Post $post,
    ): RedirectResponse
    {
        if ($form->isSubmitted() && $form->isValid()) {


            $this->entityManager->persist($post);
            $this->entityManager->flush();

            $this->addFlash('success', 'upload réussi');
            return $this->redirectToRoute('app_user_page', ['username' => $user->getUsername()], Response::HTTP_SEE_OTHER);
        }

        $this->addFlash('danger', 'upload raté');
        return $this->redirectToRoute('app_user_page', ['username' => $user->getUsername()], Response::HTTP_SEE_OTHER);

    }
}