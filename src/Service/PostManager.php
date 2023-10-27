<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\Prompt;
use App\Entity\User;
use App\Repository\PromptRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use function PHPUnit\Framework\isNull;

class PostManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PromptRepository       $promptRepository
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
        Post   $post,
        User   $postOwner,
        string $newFilename,
        string $originalFilename
    ): void
    {
        $now = new \DateTimeImmutable();

        $post->addUser($postOwner);
        $post->setUploadedOn($now);

        $foundPrompt = $this->autoPromptSelect($originalFilename);

        if (!isNull($foundPrompt)) {
            $post->setPrompt($foundPrompt);
        } else {
            $post->setPrompt(null);
        }

        // $post->setPrompt($form->get('prompt')->getData());
        $post->setFileName($newFilename);
    }

    public function autoPromptSelect(
        string $originalFilename,
    ): Prompt|null
    {
        foreach ($this->getAllPromptNames() as $prompt) {
            if (str_contains($prompt, $originalFilename)) {
                return $this->promptRepository->findOneBy(['name_fr' => $prompt]) ?? $this->promptRepository->findOneBy(['name_en' => $prompt]);
            }
        }
        return null;
    }

    private function getAllPromptNames(): array
    {
        $prompts = $this->promptRepository->findAll();
        $promptNames = [];

        foreach ($prompts as $prompt) {
            $promptNames[] = $prompt->getNameEn();
            $promptNames[] = $prompt->getNameFr();
        }
        return $promptNames;
    }
}
