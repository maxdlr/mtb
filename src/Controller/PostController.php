<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Form\PromptToPostType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Service\SecurityManager;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(
        Request                $request,
        EntityManagerInterface $entityManager,
        UserRepository         $userRepository,
        SluggerInterface       $slugger,
    ): FormInterface|RedirectResponse
    {
        $post = new Post();
        $now = new DateTimeImmutable();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

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
                $singlePost = new Post();

                $user = $userRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
                $singlePost->addUser($user);
                $singlePost->setUploadedOn($now);
                $singlePost->setPrompt($form->get('prompt')->getData());

                $singlePost->setFileName($newFilename);
                $entityManager->persist($singlePost);
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
