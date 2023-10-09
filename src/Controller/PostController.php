<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Service\SecurityManager;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostController extends AbstractController
{
    public function new(
        Request                $request,
        EntityManagerInterface $entityManager,
        SluggerInterface       $slugger,
        User                   $owner
    ): FormInterface
    {
        $post = new Post();
        $now = new DateTimeImmutable();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $postFile */

            $postFile = $form->get('post')->getData();

            if ($postFile) {
                $originalFilename = pathinfo($postFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $postFile->guessExtension();

                // Move the file to the directory where imgs are stored
                try {
                    $postFile->move(
                        $this->getParameter('posts_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', $e->getCode());
                    $this->addFlash('danger', $e->getMessage());
                    // ... handle exception if something happens during file upload
                }

                $post->addUser($owner);
                $post->setUploadedOn($now);
                $post->setPrompt($form->get('prompt')->getData());
                $post->setFileName($newFilename);

                $entityManager->persist($post);
            }
        }
        return $form;
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(
        Request                $request,
        Post                   $post,
        EntityManagerInterface $entityManager,
        SecurityManager        $securityManager
    ): Response
    {
        $owner = $post->getUser()->get(0);

        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token')) && $securityManager->isOwnerPost($post)) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_page', ['username' => $owner->getUsername()], Response::HTTP_SEE_OTHER);
    }

}
