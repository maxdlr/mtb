<?php

namespace App\Twig\Components;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent()]
final class FormComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;
    use LiveCollectionTrait;

    #[LiveProp]
    public bool $isSuccessful = false;

    public ?object $entity = null;
    #[LiveProp]
    public string $formTypeClassName;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm($this->formTypeClassName);
    }

    public function hasValidationErrors(): bool
    {
        return $this->getForm()->isSubmitted() && !$this->getForm()->isValid();
    }

//    #[LiveAction]
//    public function save(): void
//    {
//        $this->submitForm();
//
//        if (count($this->getForm()->get('prompts')->getData()) < 31) {
//            $this->addFlash(
//                'danger',
//                "Attention, tu n'as ajouté que " . count($this->getForm()->get('prompts')->getData())) . " thèmes.";
//            $this->isSuccessful = false;
//            return;
//        }
//
//        $this->entity = $this->getForm()->getData();
//
//        $this->entityManager->persist($this->entity);
//        $this->entityManager->flush();
//
//        $this->isSuccessful = true;
//    }
}
