<?php

namespace App\Twig\Components;

use App\Entity\Post;
use App\Form\AddPromptToOrphanPostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
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

    public function __construct(
        private readonly FormFactoryInterface $formFactory
    )
    {
    }

    #[LiveListener('updatePosts')]
    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->createNamed(
            'addPromptToPromptlessPost_' . $this->post->getId(),
            AddPromptToOrphanPostType::class,
            $this->post
        );
    }

    #[LiveAction]
    public function save(EntityManagerInterface $entityManager, Request $request)
    {
        $this->submitForm();

        /** @var Post $post */
        $post = $this->getForm()->getData();
        $entityManager->persist($post);
        $entityManager->flush();

        $this->addFlash('success', '"' . ucfirst($post->getPrompt()->getNameFr()) . '" enregistré sur le post !');

        return $this->redirect($request->headers->get('referer'));
    }

}
