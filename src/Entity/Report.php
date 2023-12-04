<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $post = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $reporter = null;

    #[ORM\Column(length: 500)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $reportedOn = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $resolvedOn = null;

    #[ORM\Column(nullable: false)]
    private ?bool $isResolved = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): static
    {
        $this->post = $post;

        return $this;
    }

    public function getReporter(): ?User
    {
        return $this->reporter;
    }

    public function setReporter(?User $reporter): static
    {
        $this->reporter = $reporter;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getReportedOn(): ?\DateTimeImmutable
    {
        return $this->reportedOn;
    }

    public function setReportedOn(\DateTimeImmutable $reportedOn): static
    {
        $this->reportedOn = $reportedOn;

        return $this;
    }

    public function getResolvedOn(): ?\DateTimeImmutable
    {
        return $this->resolvedOn;
    }

    public function setResolvedOn(?\DateTimeImmutable $resolvedOn): static
    {
        $this->resolvedOn = $resolvedOn;

        return $this;
    }

    public function isResolved(): bool
    {
        return $this->isResolved;
    }

    public function setIsResolved(bool $isResolved): static
    {
        $this->isResolved = $isResolved;

        return $this;
    }
}
