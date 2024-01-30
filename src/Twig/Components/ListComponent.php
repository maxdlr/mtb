<?php

namespace App\Twig\Components;

use App\Repository\PromptListRepository;
use App\Repository\PromptRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent()]
final class ListComponent
{
    public ?string $year = null;
    public ?array $prompts;

    public function __construct(
        private readonly PromptRepository $promptRepository
    )
    {
    }

    public function mount(?string $year): void
    {
        $this->year = $year;
        $this->prompts = $this->promptRepository->findByYear($year);
    }

    public function getPrompts(): array
    {
        return $this->prompts;
    }
}
