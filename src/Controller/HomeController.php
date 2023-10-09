<?php

namespace App\Controller;

use App\Repository\PromptListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PromptListRepository $promptListRepository): Response
    {
        $now = new \DateTimeImmutable();
        $currentList = $promptListRepository->findOneBy(['year' => $now->format('Y')]);

        return $this->render('home/index.html.twig', [
            'currentList' => $currentList,
        ]);
    }
}
