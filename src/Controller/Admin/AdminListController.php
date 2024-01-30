<?php

namespace App\Controller\Admin;

use App\Entity\PromptList;
use App\Form\PromptListType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminListController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route(path: '/list/new', name: 'app_list_new')]
    public function adminDashboard(): Response
    {
        $newList = new PromptList();
        $formClass = PromptListType::class;
        $list = new PromptList();
        $form = $this->createForm($formClass, $list);

        if ($form->isSubmitted() && $form->isValid()) {

            if (count($form->get('prompts')->getData()) < 31) {
                $this->addFlash(
                    'danger',
                    "Attention, tu n'as ajouté que " . count($form->get('prompts')->getData())) . " thèmes.";
            }

            $this->entityManager->persist($list);
            $this->entityManager->flush();

            $this->addFlash('success', 'Nouvelle Liste' . $list->getYear() . 'Ajouté');
            $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/list/new.html.twig', [
            'formClass' => $formClass,
            'newList' => $newList,
        ]);
    }
}