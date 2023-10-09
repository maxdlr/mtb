<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\AllPostsType;
use App\Form\PostType;
use App\Form\PromptToPostType;
use App\Repository\PromptRepository;
use App\Repository\UserRepository;
use App\Service\SecurityManager;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\UX\Turbo\TurboBundle;

#[Route('/u')]
class PageController extends AbstractController
{

    #[Route('/{username}', name: 'app_user_page', methods: ['GET', 'POST'])]
    public function index(
        UserRepository         $userRepository,
        string                 $username,
        Request                $request,
        PostController         $postController,
        EntityManagerInterface $entityManager,
        SluggerInterface       $slugger,
        SecurityManager        $securityManager
    ): Response
    {
        $owner = $userRepository->findOneBy(['username' => $username]);

        if (!$owner) {
            $this->addFlash('danger', 'Page inexistante');
            return $this->redirectToRoute('app_home');
        }

        $newPostForm = $postController->new(
            $request,
            $entityManager,
            $slugger,
            $owner
        );

        if ($newPostForm->isSubmitted() && $newPostForm->isValid() && $securityManager->userIs($owner)) {
            $entityManager->flush();
            $this->addFlash('success', 'posts uploadées');
            return $this->redirectToRoute('app_user_page', ['username' => $owner->getUsername()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('page/index.html.twig', [
            'owner' => $owner,
            'newPostForm' => $newPostForm,
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/{username}/edit', name: 'app_user_editpage', methods: ['GET', 'POST'])]
    public function editPosts(
        UserRepository         $userRepository,
        string                 $username,
        SecurityManager        $securityManager,
        Request                $request,
        EntityManagerInterface $entityManager,
        FormFactoryInterface   $formFactory,
        PostController         $postController,
        SluggerInterface       $slugger,
    ): Response|array
    {
        $owner = $userRepository->findOneBy(['username' => $username]);
        $posts = $owner->getPosts();
        $iterator = $posts->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getPrompt()->getDayNumber() < $b->getPrompt()->getDayNumber()) ? -1 : 1;
        });

        $posts = new ArrayCollection(iterator_to_array($iterator));
        $forms = [];

        $newPostForm = $postController->new(
            $request,
            $entityManager,
            $slugger,
            $owner
        );

        $formViewsAndPersistedForms = $this->persistAllPosts(
            $posts,
            $request,
            $entityManager,
            $formFactory,
            $slugger,
            $owner
        );

        foreach ($formViewsAndPersistedForms['formViews'] as $form) {
            $forms[] = $form;
        }

        foreach ($formViewsAndPersistedForms['persistedForms'] as $persistedForm) {

            if ($persistedForm) {
                $entityManager->flush();
                return $this->redirectToRoute('app_user_editpage', ['username' => $username], Response::HTTP_SEE_OTHER);
            }
        }

        if (count($posts) < 1) {
            $this->addFlash('danger', 'Aucun post a modifier !');
            return $this->redirectToRoute('app_redirect_userlogger');
        }


        if ($securityManager->userIs($owner)) {
            return $this->render('page/edit.html.twig', [
                'owner' => $owner,
                'forms' => $forms,
                'newPostForm' => $newPostForm
            ]);
        } else {
            throw $this->createAccessDeniedException();
        }
    }

    public function persistAllPosts(
        Collection             $posts,
        Request                $request,
        EntityManagerInterface $entityManager,
        FormFactoryInterface   $formFactory,
        SluggerInterface       $slugger,
        User                   $owner,
    ): array
    {
        $formViews = [];
        $submittedForms = [];

        foreach ($posts as $post) {
            $form = $formFactory->createNamed('post_' . $post->getId(), PostType::class, $post);
            $form->handleRequest($request);
            $formViews[] = $form->createView();
            $now = new DateTimeImmutable();

            if ($form->isSubmitted() && $form->isValid()) {

                // /** @var UploadedFile $imgFile */

                $postFile = $form->get('post')->getData();

                if ($postFile) {
                    $originalFilename = pathinfo($postFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $postFile->guessExtension();

                    // Move the file to the directory where imgs are stored
                    try {
                        $postFile->move(
                            $this->getParameter('posts_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    // updates the 'imgFilename' property to store the PDF file name
                    // instead of its contents
                    $singlePost = $post;

                    $singlePost->setUploadedOn($now);
                    $singlePost->setPrompt($form->get('prompt')->getData());
                    $singlePost->addUser($owner);
                    $singlePost->setFileName($newFilename);
                    $this->addFlash('success', 'post uploadées');
                    $entityManager->persist($singlePost);
                }

                $entityManager->persist($post);
                $submittedForms[] = $form;
            }

        }
        return [
            'formViews' => $formViews,
            'persistedForms' => $submittedForms
        ];
    }

}
