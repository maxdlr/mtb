<?php

namespace App\Twig\Components;

use App\Entity\Post;
use App\Entity\Report;
use App\Entity\User;
use App\Form\ReportType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;

#[AsLiveComponent()]
final class ReportComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ValidatableComponentTrait;

    public ?Report $report = null;

    #[LiveProp]
    private ?Post $post;

    public function __construct(
        private readonly FormFactoryInterface   $formFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository         $userRepository,
        private readonly PostRepository         $postRepository,
    )
    {
        $this->report = new Report();
    }

    public function setPost(#[LiveArg('post_id')] int $postId): void
    {
        $this->post = $this->postRepository->find($postId);
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ReportType::class, $this->report);
    }

    // todo: make sure ReportComponent.html.twig submit button points to this method.
    #[LiveAction]
    public function save(Request $request): RedirectResponse
    {
        $reporter = $this->userRepository->findOneByUsername($this->getUser()?->getUserIdentifier());
        if (!$reporter) {
            $this->addFlash('danger', 'Veuillez vous connecter pour signaler un post');
            return $this->redirectToRoute('app_login', ['index' => true]);
        }

        $this->submitForm();
        //todo: prompt login form if user undefined

        $this->report
            ->setReportedOn(new \DateTimeImmutable())
            ->setReporter($reporter)
            ->setPost($this->getPost());

        $this->entityManager->persist($this->report);

        dd($this->report);
        $this->entityManager->flush();

        $this->addFlash('success', 'Merci pour votre signalement !');

        return $this->redirect($request->headers->get('referer'));
    }

    private function IsUserAuthenticated(): bool|User
    {
        $reporter = $this->userRepository->findOneByUsername($this->getUser()?->getUserIdentifier());

        if ($reporter) {
            return $reporter;
        } else {
            throw new AuthenticationException('Veuillez vous connecter');
        }
    }
}
