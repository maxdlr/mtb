<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        PostRepository $postRepository,
        SluggerInterface $slugger,
    ): FormInterface
    {
        $post = new Post();
        $now = new DateTimeImmutable();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        $user = $userRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);

        if ($form->isSubmitted() && $form->isValid()) {
            // /** @var UploadedFile $imgFile */

            $postFiles = $request->files->get('post')['posts'];

            foreach ($postFiles as $postFile) {
                $singlePost = null;

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
                    $singlePost = new Post();

                    $singlePost->addUser($user);
                    $singlePost->setUploadedOn($now);

                    $singlePost->setFileName($newFilename);
                    $this->addFlash('success', 'posts uploadées');
                }

                $postRepository->save($singlePost, true);
                $this->redirectToRoute('app_user_page', ['username' => $user->getUsername()], Response::HTTP_SEE_OTHER);
            }
        }

//        $this->render('post/new.html.twig', [
//            'post' => $post,
//            'postForm' => $form,
//        ]);

        return $form;
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
