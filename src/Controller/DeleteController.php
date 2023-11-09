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
    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
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

        return $this->redirectToRoute('app_user_page', ['username' => $owner->getUserIdentifier()], Response::HTTP_SEE_OTHER);
    }
}