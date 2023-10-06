<?php

namespace App\Entity;

use App\Repository\PromptRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PromptRepository::class)]
class Prompt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name_fr = null;

    #[ORM\ManyToMany(targetEntity: PromptList::class, inversedBy: 'prompts')]
    private Collection $prompt_list;

    #[ORM\Column(length: 255)]
    private ?string $name_en = null;

    #[ORM\Column]
    private ?int $dayNumber = null;

    #[ORM\OneToMany(mappedBy: 'prompt', targetEntity: Post::class)]
    private Collection $posts;

    public function __construct()
    {
        $this->prompt_list = new ArrayCollection();
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameFr(): ?string
    {
        return $this->name_fr;
    }

    public function setNameFr(string $name_fr): static
    {
        $this->name_fr = $name_fr;

        return $this;
    }

    /**
     * @return Collection<int, PromptList>
     */
    public function getPromptList(): Collection
    {
        return $this->prompt_list;
    }

    public function addPromptList(PromptList $promptList): static
    {
        if (!$this->prompt_list->contains($promptList)) {
            $this->prompt_list->add($promptList);
        }

        return $this;
    }

    public function removePromptList(PromptList $promptList): static
    {
        $this->prompt_list->removeElement($promptList);

        return $this;
    }

    public function getNameEn(): ?string
    {
        return $this->name_en;
    }

    public function setNameEn(string $name_en): static
    {
        $this->name_en = $name_en;

        return $this;
    }

    public function getDayNumber(): ?int
    {
        return $this->dayNumber;
    }

    public function setDayNumber(int $dayNumber): static
    {
        $this->dayNumber = $dayNumber;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setPrompt($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getPrompt() === $this) {
                $post->setPrompt(null);
            }
        }

        return $this;
    }
}
