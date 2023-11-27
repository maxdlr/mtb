<?php

namespace App\Controller;

use App\Form\admin\AdminPostType;
use App\Form\admin\AdminUserType;
use App\Form\AdminPromptType;
use App\Repository\PostRepository;
use App\Repository\PromptRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'app_admin')]
class AdminController extends AbstractController
{
    public function __construct(
        private readonly PostRepository         $postRepository,
        private readonly FormFactoryInterface   $formFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository         $userRepository,
        private readonly PromptRepository       $promptRepository
    )
    {
    }

    #[Route('/{entity?}', name: '')]
    public function index(
        string  $entity = null,
        Request $request,
    ): Response
    {
        $entities = new ArrayCollection([
            'posts' => new ArrayCollection([
                'name' => 'posts',
                'collection' => $this->postRepository->findAll(),
                'formType' => AdminPostType::class
            ]),
            'users' => new ArrayCollection([
                'name' => 'users',
                'collection' => $this->userRepository->findAll(),
                'formType' => AdminUserType::class
            ]),
            'prompts' => new ArrayCollection([
                'name' => 'prompts',
                'collection' => $this->promptRepository->findAll(),
                'formType' => AdminPromptType::class
            ]),
        ]);

        $names = [];
        $formViews = [];

        foreach ($entities->getKeys() as $entityName) {
            $collection = $entities->get($entityName)->get('collection');
            $formType = $entities->get($entityName)->get('formType');
            $names[] = $entities->get($entityName)->get('name');

            if ($entity == $entityName) {
                $forms = $this->generateAdminForms($collection, $formType);
                $this->handleAdminFormRequests($forms, $request);
                $formViews = $this->generateAdminFormViews($forms);
                if ($this->persistAdminFormObjects($forms, $collection)) {
                    $this->addFlash('success', "C'est modifié !");
                    return $this->redirectToRoute('app_redirect_referer');
                }
            }
        }

        return $this->render('admin/index.html.twig', [
            'forms' => $formViews,
            'names' => $names,
        ]);
    }

    public function generateAdminForms($objects, $formType): array
    {
        $forms = [];
        foreach ($objects as $object) {
            $forms[] = $this->formFactory->createNamed('admin-post-form-' . $object->getId(), $formType, $object);
        }
        return $forms;
    }

    public function generateAdminFormViews(array $forms): array
    {
        $formViews = [];
        foreach ($forms as $form) {
            $formViews[] = $form->createView();
        }
        return $formViews;
    }

    public function persistAdminFormObjects(array $forms, array $collection): bool
    {
        $associatedFormsAndObjects = [];

        for ($i = 0; $i < count($collection) - 1; $i++) {
            $associatedFormsAndObjects[] = ['object' => $collection[$i], 'form' => $forms[$i]];
        }

        foreach ($associatedFormsAndObjects as $objectAndForm) {
            if ($objectAndForm['form']->isSubmitted() && $objectAndForm['form']->isValid()) {
                $this->entityManager->persist($objectAndForm['object']);
                $this->entityManager->flush();
                return true;
            }
        }
        return false;
    }

    public function handleAdminFormRequests(array $forms, Request $request): void
    {
        foreach ($forms as $form) {
            $form->handleRequest($request);
        }
    }

    // ------------------------------------------------------------------

}
