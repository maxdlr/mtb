<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route(path: '/admin', name: 'app_admin')]
    public function adminDashboard(): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', "T'as pas le droit.");
            return $this->redirectToRoute('app_login');
        }

        return $this->render('admin/dashboard.html.twig');
    }
}