<?php

namespace App\Twig\Components;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Service\FileUploadManager;
use App\Service\PostManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent()]
final class UploadPostsComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ?User $owner = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(PostType::class);
    }

    #[LiveAction]
    public function save(
        Request                $request,
        FileUploadManager      $fileUploadManager,
        PostManager            $postManager,
        EntityManagerInterface $entityManager
    ): RedirectResponse
    {
        $this->submitForm();

        $multiple = $request->files->get('post')['posts'];
        foreach ($multiple as $file) {
            if ($file instanceof UploadedFile) {
                $newFilename = $fileUploadManager->upload($file);
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $singlePost = new Post();
                $postManager->setPost($singlePost, $this->getOwner(), $newFilename, $originalFilename);
                $entityManager->persist($singlePost);
                $entityManager->flush();
            }
        }
        return $this->redirectToRoute('app_redirect_referer');
    }

    public function getOwner(): User|null
    {
        return $this->owner;
    }
}
