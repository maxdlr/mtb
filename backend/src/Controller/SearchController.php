<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('', name: 'app_search_')]
class SearchController extends AbstractController
{
    #[Route('/p/search', name: 'index')]
    public function index(): Response
    {
        return $this->render('/search/index.html.twig');
    }
}