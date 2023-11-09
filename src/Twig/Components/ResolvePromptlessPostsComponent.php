<?php

namespace App\Twig\Components;

use App\Entity\Post;
use App\Form\AddPromptToOrphanPostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent()]
final class ResolvePromptlessPostsComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ?Post $post = null;
    private AddPromptToOrphanPostType $addPromptToOrphanPostType;

    public function __construct(private readonly FormFactoryInterface $formFactory)
    {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->createNamed(
            'addPromptToPromptlessPost_' . $this->post->getId(),
            AddPromptToOrphanPostType::class,
            $this->post
        );
    }

    #[LiveAction]
    public function save(EntityManagerInterface $entityManager)
    {
        $this->submitForm();

        /** @var Post $post */
        $post = $this->getForm()->getData();
        $entityManager->persist($post);
        $entityManager->flush();

        $this->addFlash('success', 'Post saved!');

        return $this->redirectToRoute('app_redirect_referer');
    }

}
