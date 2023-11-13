<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\UserRepository;
use App\Service\FileUploadManager;
use App\Service\PostManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post', name: 'app_post_')]
class PostController extends AbstractController
{
    private ?Collection $messages;

    public function __construct(
        private readonly UserRepository         $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly FileUploadManager      $fileUploadManager,
        private readonly PostManager            $postManager
    )
    {
        $this->messages = new ArrayCollection();
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
            $this->validatePost($postFile);
            if (!$this->messages->isEmpty())
                return $this->json($this->getMessages());
            $fileSize = $postFile->getSize();
            $newFilename = $this->fileUploadManager->upload($postFile);
            $originalFilename = pathinfo($postFile->getClientOriginalName(), PATHINFO_FILENAME);
            $this->postManager->setPost($post, $owner, $newFilename, $originalFilename, $fileSize);
            $this->entityManager->persist($post);

            $this->entityManager->flush();
        }
        return $this->json($this->getMessages());
    }

    #[Route('/upload/{username}/confirm/{number}', name: 'upload_confirm', methods: ['GET', 'POST'])]
    public function uploadConfirmAll(
        string $username,
        int    $number
    ): JsonResponse
    {
        $owner = $this->userRepository->findOneByUsername($username);
        $promptLessPosts = $this->postManager->getPromptlessPosts($owner->getPosts())->count();
        $this->validateUploadRequest($number, $promptLessPosts);

        return $this->json($this->getMessages());
    }

//    ----------------------------------------------------------------------------

    public function validatePost($uploadedPost): void
    {
        $notGifPost = '';
        if ($uploadedPost->getClientOriginalExtension() != 'gif') {
            $notGifPost .= $uploadedPost->getClientOriginalName() . ' ';
            $this->messages->add(['type' => 'danger', 'message' => "Nous n'acceptons que les GIFs, sorry ! $notGifPost n'est/ne sont pas un/des gif"]);
        }

        if (!$uploadedPost instanceof UploadedFile)
            $this->messages->add(['type' => 'danger', 'message' => 'Attention ceci n\'est pas un post']);

        if ($uploadedPost->getSize() > 40000000)
            $this->messages->add(['type' => 'danger', 'message' => 'Fichier trop lourd. ' . ($uploadedPost->getSize() / 1000000) . ' MO. 40MO Maximum autorisé.']);

    }

    public function validateUploadRequest(
        $numberOfPosts,
        $numberOfPromptlessPost
    ): void
    {
        if ($numberOfPosts === 0)
            $this->messages->add(['type' => 'danger', 'message' => 'Aucun fichier selectionné.']);

        if ($numberOfPosts !== 0) {
            if ($numberOfPosts < 2)
                $this->messages->add(['type' => 'success', 'message' => 'Ton post est bien mis en ligne !']);

            if ($numberOfPosts > 1)
                $this->messages->add(['type' => 'success', 'message' => "$numberOfPosts posts ont bien été mis en ligne !"]);
        }

        if ($numberOfPromptlessPost > 0)
            $this->messages->add(['type' => 'warning', 'message' => "$numberOfPromptlessPost posts sans thème."]);

    }

    public function getMessages(): ?Collection
    {
        return $this->messages;
    }
}