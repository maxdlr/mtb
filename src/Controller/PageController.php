<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\PromptListRepository;
use App\Repository\UserRepository;
use App\Service\DataManager;
use App\Service\PostManager;
use App\Service\SecurityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/u')]
class PageController extends AbstractController
{

    #[Route('/{username}', name: 'app_user_page', methods: ['GET', 'POST'])]
    public function index(
        UserRepository         $userRepository,
        string                 $username,
        Request                $request,
        PostManager            $postManager,
        EntityManagerInterface $entityManager,
        SluggerInterface       $slugger,
        SecurityManager        $securityManager,
        PostRepository         $postRepository,
        PromptListRepository   $promptListRepository
    ): Response
    {
        $owner = $userRepository->findOneBy(['username' => $username]);
        $user = $userRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $promptLists = $promptListRepository->findAll();
        $posts = $postRepository->findAllBy('user.username', $username, 'prompt.dayNumber');

        if (!$owner) {
            $this->addFlash('danger', 'Page inexistante');
            return $this->redirectToRoute('app_home');
        }

        $newPostForm = $postManager->new(
            $request,
            $entityManager,
            $slugger,
            $owner
        );

        if ($newPostForm->isSubmitted() && $newPostForm->isValid() && $securityManager->userIsOwner($user, $owner)) {
            $entityManager->flush();
            $this->addFlash('success', 'posts uploadées');
            return $this->redirectToRoute('app_user_page', ['username' => $owner->getUsername()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('page/index.html.twig', [
            'promptLists' => $promptLists,
            'posts' => $posts,
            'owner' => $owner,
            'newPostForm' => $newPostForm,
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/{username}/edit', name: 'app_user_page_edit', methods: ['GET', 'POST'])]
    public function edit(
        UserRepository         $userRepository,
        string                 $username,
        SecurityManager        $securityManager,
        Request                $request,
        EntityManagerInterface $entityManager,
        FormFactoryInterface   $formFactory,
        PostManager            $postManager,
        SluggerInterface       $slugger,
        DataManager            $dataManager
    ): Response|array
    {
        $user = $userRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $owner = $userRepository->findOneBy(['username' => $username]);
        $posts = $dataManager->sortPostsByDayNumber($owner->getPosts());

        $newPostForm = $postManager->new($request, $entityManager, $slugger, $owner);
        $editPostForms = $postManager->createEditPostsForms($posts, $request, $entityManager, $formFactory, $slugger, $owner);
        $forms = $postManager->extractFromEditPostsForms($editPostForms, 'formViews');
        $persistedForms = $postManager->extractFromEditPostsForms($editPostForms, 'persistedForms');

        if ($posts->isEmpty()) {
            $this->addFlash('danger', 'Aucun post a modifier !');
            return $this->redirectToRoute('app_redirect_userlogger');
        }

        if ($postManager->flushEditedPosts($persistedForms, $entityManager)) {
            return $this->redirectToRoute('app_user_page_edit', ['username' => $owner->getUsername()]);
        };

        if ($securityManager->userIsOwner($user, $owner)) {
            return $this->render('page/edit.html.twig', [
                'owner' => $owner,
                'forms' => $forms,
                'newPostForm' => $newPostForm
            ]);
        } else {
            $this->addFlash('danger', 'Tu ne peux pas modifier les posts qui ne sont pas à toi !');
            return $this->redirectToRoute('app_user_page', ['username' => $owner->getUsername()]);
        }
    }

}
