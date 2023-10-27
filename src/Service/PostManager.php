<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;

class PostManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    public function extractFromEditPostsForms(
        array  $createdForms,
        string $key
    ): array
    {
        $forms = [];
        foreach ($createdForms[$key] as $form) {
            $forms[] = $form;
        }
        return $forms;
    }

    public function flushPosts(
        array $forms,
    ): void
    {
        foreach ($forms as $ignored) {
            $this->entityManager->flush();
        }
    }

    public function setPost(
        Post          $post,
        User          $postOwner,
        FormInterface $form,
        string        $newFilename
    ): void
    {
        $now = new \DateTimeImmutable();

        $post->addUser($postOwner);
        $post->setUploadedOn($now);
        $post->setPrompt($form->get('prompt')->getData());
        $post->setFileName($newFilename);
    }
}
