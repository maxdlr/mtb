<?php

namespace App\Twig\Components;

use App\Entity\Post;
use App\Entity\Report;
use App\Form\AddPromptToOrphanPostType;
use App\Form\ReportType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent()]
final class ReportComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public ?int $postId = null;
    public ?Report $report = null;

    public function __construct(
        private readonly FormFactoryInterface $formFactory
    )
    {
        $this->report = new Report();
    }

    #[LiveListener('setPostToReportForm')]
    public function setPost(#[LiveArg] int $postId): void
    {
        $this->postId = $postId;
    }

    #[LiveListener('updatePosts')]
    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->createNamed(
            'report_' . $this->postId,
            ReportType::class,
            $this->report,
            ['post_id' => $this->postId]
        );
    }
}
