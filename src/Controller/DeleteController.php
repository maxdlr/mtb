<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\UserRepository;
use App\Service\SecurityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/delete')]
class DeleteController extends AbstractController
{
    private ?Collection $messages;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    #[Route('/post/{id}/{username}/{token}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(
        Post                   $post,
        EntityManagerInterface $entityManager,
        UserRepository         $userRepository,
        SecurityManager        $securityManager,
        string                 $username,
        string                 $token
    ): JsonResponse
    {
        $owner = $userRepository->findOneByUsername($username);
        $postPromptName = ucfirst($post->getPrompt()->getNameFr());

        if ($securityManager->isOwnerOfPost($owner, $post)) {
            if ($this->isCsrfTokenValid('delete' . $post->getId(), $token)) {
                $entityManager->remove($post);
                $entityManager->flush();

                $this->messages->add(['type' => 'success', 'message' => "Post du thème $postPromptName supprimé !"]);
            }
        } else {
            $this->messages->add(['type' => 'danger', 'message' => 'T\'est pas propriétaire du post fréro']);
        }
        return $this->json($this->getMessages());
    }

    #[Route('/posts/{username}/{token}', name: 'app_post_delete_all', methods: ['POST'])]
    public function deleteAll(
        EntityManagerInterface $entityManager,
        UserRepository         $userRepository,
        SecurityManager        $securityManager,
        string                 $username,
        string                 $token
    ): Response
    {
        $owner = $userRepository->findOneByUsername($username);
        $ownerPosts = $owner->getPosts();
        $deletedPostCount = 0;

        foreach ($ownerPosts as $post) {
            $postPromptName = ucfirst($post?->getPrompt()?->getNameFr());

            if ($securityManager->isOwnerOfPost($owner, $post)) {
                if ($this->isCsrfTokenValid('deleteAllPosts', $token)) {
                    $entityManager->remove($post);
                    $entityManager->flush();
                    $deletedPostCount++;
                }
            } else {
                $this->messages->add(['type' => 'danger', 'message' => "Le post du thème $postPromptName n'a pas été supprimé, tu n'es pas le propiétaire fréro !"]);
            }
        }
        $this->messages->add(['type' => 'success', 'message' => "$deletedPostCount posts supprimés !"]);

        return $this->json($this->getMessages());
    }

    public function getMessages(): ?Collection
    {
        return $this->messages;
    }
}