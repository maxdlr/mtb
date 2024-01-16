<?php

namespace App\Twig\Components;

use App\Repository\PromptListRepository;
use App\Repository\PromptRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent()]
final class ListComponent
{
    use DefaultActionTrait;

    public string $year;
    public string $listYear;
    public array $prompts;

    public function __construct(
        private readonly PromptListRepository $promptListRepository,
        private readonly PromptRepository     $promptRepository
    )
    {
        $this->listYear = $this->promptListRepository->findOneBy(['year' => $this->year])->getYear();
        $this->prompts = $this->promptRepository->findByYear($this->year);
    }

}
