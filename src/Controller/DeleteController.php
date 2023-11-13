<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\UserRepository;
use App\Service\SecurityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/delete')]
class DeleteController extends AbstractController
{
    #[Route('/post/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(
        Request                $request,
        Post                   $post,
        EntityManagerInterface $entityManager,
        UserRepository         $userRepository,
        SecurityManager        $securityManager
    ): Response
    {
        $owner = $userRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);

        if ($securityManager->isOwnerOfPost($owner, $post)) {
            if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
                $entityManager->remove($post);
                $entityManager->flush();
                $this->addFlash('success', 'Post supprimé !');
            }
        } else {
            $this->addFlash('danger', 'T\'est pas propriétaire du post fréro');
        }

        return $this->redirectToRoute('app_redirect_referer');
    }

    #[Route('/posts', name: 'app_post_delete_all', methods: ['POST'])]
    public function deleteAll(
        Request                $request,
        EntityManagerInterface $entityManager,
        UserRepository         $userRepository,
        SecurityManager        $securityManager
    ): Response
    {
        $owner = $userRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);

        $ownerPosts = $owner->getPosts();

        foreach ($ownerPosts as $post) {
            if ($securityManager->isOwnerOfPost($owner, $post)) {
                if ($this->isCsrfTokenValid('deleteAllPosts', $request->request->get('_token'))) {
                    $entityManager->remove($post);
                    $entityManager->flush();
                }
            } else {
                $this->addFlash('danger', 'T\'est pas propriétaire du post fréro');
            }
            $this->addFlash('success', 'Posts supprimés !');
        }
        return $this->redirectToRoute('app_redirect_referer');
    }
}