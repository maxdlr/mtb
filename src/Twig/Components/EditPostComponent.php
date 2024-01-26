<?php

namespace App\Twig\Components;

use App\Entity\Post;
use App\Form\EditPostType;
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
final class EditPostComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ?Post $post = null;
    private EditPostType $editPostType;

    public function __construct(private readonly FormFactoryInterface $formFactory)
    {
    }

    #[LiveListener('updatePosts')]
    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->createNamed(
            'editPost_' . $this->post->getId(),
            EditPostType::class,
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

        $this->addFlash('success', 'Post "' . ucfirst($post->getPrompt()->getNameFr()) . '" modifié !');

        return $this->redirectToRoute('app_redirect_referer');
    }

}
