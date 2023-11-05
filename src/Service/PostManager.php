<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Exception;

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
            'owner' => $post?->getUser()[0]->getUsername(),
            'dayNumber' => $post?->getPrompt()->getDayNumber(),
            'promptNameFr' => $post?->getPrompt()->getNameFr(),
            'promptListYear' => $post?->getPrompt()->getPromptList(), //array
            'date' => $post?->getUploadedOn(),
            'id' => $post?->getId(),
        ];
    }
}
