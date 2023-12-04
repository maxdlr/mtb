<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\AddPromptToOrphanPostType;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Repository\PromptListRepository;
use App\Repository\UserRepository;
use App\Service\DataManager;
use App\Service\FileUploadManager;
use App\Service\PostManager;
use App\Service\PromptListManager;
use App\Service\SecurityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use function PHPUnit\Framework\isEmpty;

#[Route('/u')]
class PageController extends AbstractController
{
    public \DateTimeImmutable $now;

    public function __construct(
        private readonly FileUploadManager      $fileUploadManager,
        private readonly EntityManagerInterface $entityManager,
        private readonly FormFactoryInterface   $formFactory,
        private readonly PostManager            $postManager,
        private readonly PromptListManager      $promptListManager,
        private readonly UserRepository         $userRepository,
        private readonly PostRepository         $postRepository
    )
    {
        $this->now = new \DateTimeImmutable();
    }

    #[Route('/{username}', name: 'app_user_page', methods: ['GET', 'POST'])]
    public function index(
        string         $username,
        PostRepository $postRepository,
    ): Response
    {
        $owner = $this->userRepository->findOneBy(['username' => $username]);
        $user = $this->userRepository->findOneBy(['username' => $this->getUser()?->getUserIdentifier()]);

        if (!$owner) {
            $this->addFlash('danger', 'Page inexistante');
            return $this->redirectToRoute('app_home');
        }

        $posts = $postRepository->findAllBy('user.username', $username, 'prompt.dayNumber');
        $promptLists = $this->promptListManager->getPostsPromptLists($posts);
        $promptlessPosts = $this->postManager->getPromptlessPosts($owner->getPosts());

        $form = $this->createForm(PostType::class);

        return $this->render('page/index.html.twig', [
            'promptLists' => $promptLists,
            'posts' => $posts,
            'owner' => $owner,
            'promptlessPosts' => $promptlessPosts,
            'form' => $form
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/{username}/edit', name: 'app_user_page_edit', methods: ['GET', 'POST'])]
    public function edit(
        string          $username,
        SecurityManager $securityManager,
        PostManager     $postManager,
        Request         $request
    ): Response|array
    {
        $user = $this->userRepository->findOneBy(['username' => $this->getUser()?->getUserIdentifier()]);
        $owner = $this->userRepository->findOneBy(['username' => $username]);
        $posts = $postManager->sortPostsByDayNumber($owner->getPosts());
        $promptlessPosts = $postManager->getPromptlessPosts($posts);

        $editPostForms = $this->createEditPostsForms($posts, $owner, $request);
        $forms = $postManager->extractForms($editPostForms, 'formViews');
        $persistedForms = $postManager->extractForms($editPostForms, 'persistedForms');

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
                'promptlessPosts' => $promptlessPosts
            ]);
        } else {
            $this->addFlash('danger', 'Tu ne peux pas modifier les posts qui ne sont pas à toi !');
            return $this->redirectToRoute('app_redirect_user_fallback');
        }
    }

    // -------------------------------------------------------------

    public function createEditPostsForms(
        Collection $posts,
        User       $owner,
        Request    $request
    ): array
    {
        $formViews = [];
        $persistedForms = [];

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
                $persistedForms[] = $form;
            }
        }
        return [
            'formViews' => $formViews,
            'persistedForms' => $persistedForms
        ];
    }

}
