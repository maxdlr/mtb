<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Repository\PromptListRepository;
use App\Repository\UserRepository;
use App\Service\DataManager;
use App\Service\FileUploadManager;
use App\Service\PostManager;
use App\Service\SecurityManager;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/u')]
class PageController extends AbstractController
{
    public \DateTimeImmutable $now;

    public function __construct(
        private readonly FileUploadManager      $fileUploadManager,
        private readonly EntityManagerInterface $entityManager,
        private readonly FormFactoryInterface   $formFactory,
        private readonly PostManager            $postManager
    )
    {
        $this->now = new \DateTimeImmutable();
    }

    #[Route('/{username}', name: 'app_user_page', methods: ['GET', 'POST'])]
    public function index(
        UserRepository       $userRepository,
        string               $username,
        SecurityManager      $securityManager,
        PostRepository       $postRepository,
        PromptListRepository $promptListRepository,
        Request              $request
    ): Response
    {
        $owner = $userRepository->findOneBy(['username' => $username]);
        $promptLists = $promptListRepository->findAll();
        $posts = $postRepository->findAllBy('user.username', $username, 'prompt.dayNumber');
        $newPostForm = $this->newPost($owner, $request);
        $user = $userRepository->findOneBy(['username' => $this->getUser()?->getUserIdentifier()]);

        if (!$owner) {
            $this->addFlash('danger', 'Page inexistante');
            return $this->redirectToRoute('app_home');
        }

        if ($newPostForm->isSubmitted() && $newPostForm->isValid() && $securityManager->userIsOwner($user, $owner)) {
            $this->entityManager->flush();
            $this->addFlash('success', 'posts uploadées');
            return $this->redirectToRoute('app_user_page', ['username' => $user->getUsername()], Response::HTTP_SEE_OTHER);
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
        UserRepository  $userRepository,
        string          $username,
        SecurityManager $securityManager,
        PostManager     $postManager,
        DataManager     $dataManager,
        Request         $request
    ): Response|array
    {
        $user = $userRepository->findOneBy(['username' => $this->getUser()?->getUserIdentifier()]);
        $owner = $userRepository->findOneBy(['username' => $username]);
        $posts = $dataManager->sortPostsByDayNumber($owner->getPosts());

        $newPostForm = $this->newPost($owner, $request);
        $editPostForms = $this->createEditPostsForms($posts, $owner, $request);
        $forms = $postManager->extractFromEditPostsForms($editPostForms, 'formViews');
        $persistedForms = $postManager->extractFromEditPostsForms($editPostForms, 'persistedForms');

        if ($posts->isEmpty()) {
            $this->addFlash('danger', 'Aucun post a modifier !');
            return $this->redirectToRoute('app_redirect_user_fallback');
        }

        if ($persistedForms) {
            try {
                $postManager->flushPosts($persistedForms);
                $this->addFlash('success', 'Post modifié !');
                return $this->redirectToRoute('app_user_page_edit', ['username' => $owner->getUsername()]);
            } catch (\Exception $e) {
                dump($e);
                $this->addFlash('danger', 'Aucun post modifié');
            }
        }

        if ($securityManager->userIsOwner($user, $owner)) {
            return $this->render('page/edit.html.twig', [
                'owner' => $owner,
                'forms' => $forms,
                'newPostForm' => $newPostForm
            ]);
        } else {
            $this->addFlash('danger', 'Tu ne peux pas modifier les posts qui ne sont pas à toi !');
            return $this->redirectToRoute('app_redirect_user_fallback');
        }
    }

    // -------------------------------------------------------------

    private function newPost(
        User    $owner,
        Request $request
    ): FormInterface
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $postFile */

            $postFile = $form->get('post')->getData();

            if ($postFile) {
                $newFilename = $this->fileUploadManager->upload($postFile);
                $originalFilename = pathinfo($postFile->getClientOriginalName(), PATHINFO_FILENAME);

                $this->postManager->setPost($post, $owner, $newFilename, $originalFilename);
                $this->entityManager->persist($post);
            }
        }
        return $form;
    }

    public function createEditPostsForms(
        Collection $posts,
        User       $owner,
        Request    $request
    ): array
    {
        $formViews = [];
        $submittedForms = [];

        foreach ($posts as $post) {
            $form = $this->formFactory->createNamed('post_' . $post->getId(), PostType::class, $post);
            $form->handleRequest($request);
            $formViews[] = $form->createView();

            if ($form->isSubmitted() && $form->isValid()) {

                $postFile = $form->get('post')->getData();

                if ($postFile) {
                    $newFilename = $this->fileUploadManager->upload($postFile);
                    $originalFilename = pathinfo($postFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $this->postManager->setPost($post, $owner, $newFilename, $originalFilename);
                    $this->entityManager->persist($post);
                }
                $this->entityManager->persist($post);
                $submittedForms[] = $form;
            }
        }
        return [
            'formViews' => $formViews,
            'persistedForms' => $submittedForms
        ];
    }

}
