<?php

namespace App\Controller;

use App\Form\admin\AdminPostType;
use App\Form\admin\AdminUserType;
use App\Repository\PostRepository;
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
        private readonly UserRepository         $userRepository
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
        ]);

        $names = [];
        $formViews = [];

        foreach ($entities->getKeys() as $entityName) {
            $collection = $entities->get($entityName)->get('collection');
            $formType = $entities->get($entityName)->get('formType');
            $names[] = $entities->get($entityName)->get('name');

            if ($entity == $entityName) {
                $forms = $this->generateAdminForms($collection, $formType);

                foreach ($forms as $form) {
                    $formViews[] = $form->createView();
                    $form->handleRequest($request);
                }

                foreach ($collection as $object) {
                    $this->entityManager->persist($object);
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

    public function manageAdminForms(Request $request, array $forms, $object): void
    {


    }

    // ------------------------------------------------------------------

}
