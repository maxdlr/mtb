<?php

namespace App\Twig\Components;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent()]
final class FormComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    /**
     * @var true
     */
    public bool $isSuccessful = false;

    public ?object $entity = null;
    public string $typeClass;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm($this->typeClass);
    }

    public function hasValidationErrors(): bool
    {
        return $this->getForm()->isSubmitted() && !$this->getForm()->isValid();
    }

    #[LiveAction]
    public function save(): void
    {
        $this->submitForm();

        $this->entity = $this->getForm()->getData();

        $this->entityManager->persist($this->entity);
        $this->entityManager->flush();

        $this->isSuccessful = true;
    }

}
