<?php

namespace App\Twig\Components;

use App\Entity\Post;
use App\Entity\Report;
use App\Form\ReportType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent()]
final class ReportComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(updateFromParent: true)]
    public ?int $postId = null;

    public ?Report $report = null;

    private ?Post $post = null;

    public function __construct(
        private readonly FormFactoryInterface   $formFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository         $userRepository,
        private readonly PostRepository         $postRepository
    )
    {
        $this->report = new Report();
        $this->post = $this->postRepository->findOneBy(['id' => $this->getPostId()]);
    }

    #[LiveListener('setPostToReportForm')]
    public function setPost(#[LiveArg('post_id')] int $postId): void
    {
        $this->postId = $postId;
    }

    public function getPostId(): int|null
    {
        return $this->postId;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ReportType::class, $this->report);
    }

    // todo: make sure ReportComponent.html.twig submit button points to this method.
    #[LiveAction]
    public function save(Request $request): RedirectResponse
    {
        $this->submitForm();
        $reporter = $this->userRepository->findOneByUsername($this->getUser()?->getUserIdentifier());
        //todo: prompt login form if user undefined

        $this->report
            ->setReportedOn(new \DateTimeImmutable())
            ->setReporter($reporter)
            ->setPost($this->post);

        $this->entityManager->persist($this->report);
        $this->entityManager->flush();

        $this->addFlash('success', 'Merci pour votre signalement !');

        return $this->redirect($request->headers->get('referer'));

    }
}
