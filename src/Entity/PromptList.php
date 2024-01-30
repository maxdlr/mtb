<?php

namespace App\Entity;

use App\Repository\PromptListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PromptListRepository::class)]
class PromptList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 4)]
    private ?string $year = null;

    #[ORM\ManyToMany(targetEntity: Prompt::class, mappedBy: 'promptList', cascade: ['persist'])]
    private Collection $prompts;

    public function __construct()
    {
        $this->prompts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): static
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return Collection<int, Prompt>
     */
    public function getPrompts(): Collection
    {
        return $this->prompts;
    }

    public function addPrompt(Prompt $prompt): static
    {
        if (!$this->prompts->contains($prompt)) {
            $this->prompts->add($prompt);
            $prompt->addPromptList($this);
        }

        return $this;
    }

    public function removePrompt(Prompt $prompt): static
    {
        if ($this->prompts->removeElement($prompt)) {
            $prompt->removePromptList($this);
        }

        return $this;
    }
}
