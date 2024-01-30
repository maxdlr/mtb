<?php

namespace App\Controller\Admin;

use App\Entity\PromptList;
use App\Form\PromptListType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminListController extends AbstractController
{
    #[Route(path: '/list/new', name: 'app_list_new')]
    public function adminDashboard(): Response
    {
        $newList = new PromptList();
        $form = PromptListType::class;

        return $this->render('admin/list/new.html.twig', [
            'form' => $form,
            'newList' => $newList,
        ]);
    }
}