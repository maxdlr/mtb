<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\Prompt;
use App\Entity\User;
use App\Repository\PromptRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class PostManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PromptRepository       $promptRepository
    )
    {
    }

    public function extractForms(
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

        $post->setPrompt($foundPrompt ?? null);

        // $post->setPrompt($form->get('prompt')->getData());
        $post->setFileName($newFilename);
    }

    public function autoPromptSelect(
        string $originalFilename,
    ): Prompt|null
    {
        foreach ($this->getAllPromptNames() as $prompt) {
            if (str_contains($originalFilename, $prompt)) {
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

    /**
     * @throws Exception
     */
    public function sortPostsByDayNumber(
        Collection $postCollection,
    ): Collection
    {
        $iterator = $postCollection->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getPrompt()->getDayNumber() < $b->getPrompt()->getDayNumber()) ? -1 : 1;
        });

        return new ArrayCollection(iterator_to_array($iterator));
    }

    public function getLatestPost(?Collection $posts): ?Post
    {
        $orderBy = (Criteria::create())->orderBy([
            'uploadedOn' => Criteria::DESC,
        ]);

        return $posts?->matching($orderBy)[0];
    }

    public function PostToArray(?Post $post): array
    {
        return [
            'fileName' => $post?->getFileName(),
            'owner' => $post?->getUser()[0]?->getUsername(),
            'dayNumber' => $post?->getPrompt()?->getDayNumber(),
            'promptNameFr' => $post?->getPrompt()?->getNameFr(),
            'promptListYear' => $post?->getPrompt()?->getPromptList(), //array
            'date' => $post?->getUploadedOn(),
            'id' => $post?->getId(),
        ];
    }

    public function getOrphanPosts(
        Collection $postCollection,
    ): Collection
    {
        $orphanPosts = [];
        foreach ($postCollection as $post) {
                $post->getPrompt() ?? $orphanPosts[] = $post;
        }
        return new ArrayCollection($orphanPosts);
    }
}
