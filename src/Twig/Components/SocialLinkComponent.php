<?php

namespace App\Twig\Components;

use App\Repository\SocialLinkRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent()]
final class SocialLinkComponent
{
    private array $socialLinks;

    public function __construct(private readonly SocialLinkRepository $socialLinkRepository)
    {
    }

    public function mount(): void
    {
        $this->socialLinks = $this->socialLinkRepository->findAll();
    }

    public function getSocialLinks(): array
    {
        return $this->socialLinks;
    }
}
