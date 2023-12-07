<?php

namespace App\Twig\Components;

use App\Entity\Post;
use App\Entity\Report;
use App\Entity\User;
use App\Form\AddPromptToOrphanPostType;
use App\Form\ReportType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
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
    private ?User $reporter = null;
    private ?Post $post = null;

    public function __construct(
        private readonly FormFactoryInterface   $formFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository         $userRepository,
        private readonly PostRepository         $postRepository
    )
    {
        $this->report = new Report();
        $this->post = $this->postRepository->findOneBy(['id' => $this->postId]);
    }

    #[LiveListener('setPostToReportForm')]
    public function setPost(#[LiveArg('post_id')] int $postId): void
    {
        $this->postId = $postId;
    }

    #[LiveAction]
    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->createNamed(
            'report_' . $this->postId,
            ReportType::class,
            $this->report,
            ['post_id' => $this->postId]
        );
    }

    #[LiveAction]
    public function save()
    {
        $this->submitForm();
        $this->reporter = $this->userRepository->findOneByUsername($this->getUser()?->getUserIdentifier());

        $this->report
            ->setReportedOn(new \DateTimeImmutable())
            ->setReporter($this->reporter)
            ->setPost($this->post);

        $this->entityManager->persist($this->report);
        $this->entityManager->flush();

        $this->addFlash('success', 'Merci pour votre signalement !');

        return $this->redirectToRoute('app_redirect_user_fallback');
    }
}
