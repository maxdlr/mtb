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
//        UploadedFile $postFile
    ): JsonResponse
    {
        $postFile = $request->request;
        dump($postFile);
        $owner = $this->userRepository->findOneByUsername($username);
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

//        if ($form->isSubmitted() && $form->isValid()) {

        if (isset($postFile)) {
            $newFilename = $this->fileUploadManager->upload($postFile);
            $originalFilename = pathinfo($postFile->getClientOriginalName(), PATHINFO_FILENAME);
            $this->postManager->setPost($post, $owner, $newFilename, $originalFilename);
            $this->entityManager->persist($post);

            $this->entityManager->flush();
            return $this->json(['message' => $originalFilename . 'uploadé !']);
        }
//        }
        return $this->json(['message' => 'Fichier non conforme !']);
    }
}