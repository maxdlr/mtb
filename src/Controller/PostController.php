<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\UserRepository;
use App\Service\FileUploadManager;
use App\Service\PostManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post', name: 'app_post_')]
class PostController extends AbstractController
{
    public function __construct(
        private readonly UserRepository         $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly FileUploadManager      $fileUploadManager,
        private readonly PostManager            $postManager
    )
    {
    }

    #[Route('/upload/{username}', name: 'new', methods: ['GET', 'POST'])]
    public function uploadPost(
        string  $username,
        Request $request,
    ): JsonResponse
    {
        $postFile = $request->files->all()['post']['posts'][0];

        $owner = $this->userRepository->findOneByUsername($username);
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($postFile) {
            $newFilename = $this->fileUploadManager->upload($postFile);
            $originalFilename = pathinfo($postFile->getClientOriginalName(), PATHINFO_FILENAME);
            $this->postManager->setPost($post, $owner, $newFilename, $originalFilename);
            $this->entityManager->persist($post);

            $this->entityManager->flush();
            return $this->json(['message' => $originalFilename . ' uploadé !'], 200);
        }
        return $this->json(['message' => 'Fichier non conforme !'], 500);
    }

    #[Route('/upload/{username}/confirm/{number}', name: 'upload_confirm', methods: ['GET', 'POST'])]
    public function uploadConfirmAll(
        string $username,
        int    $number
    ): JsonResponse
    {
        $owner = $this->userRepository->findOneByUsername($username);
        $promptLessPosts = $this->postManager->getOrphanPosts($owner->getPosts())->count();


        if ($number < 1) {
            $message = "Ton post est bien mis en ligne ! Mais tu as encore $promptLessPosts posts sans thème.";
        } else {
            $message = "$number posts ont bien été mis en ligne ! Mais tu as encore $promptLessPosts posts sans thème.";
        }

        $this->addFlash('success', $message);

        return $this->json([
            'message' => $message
        ], 500);


    }
}