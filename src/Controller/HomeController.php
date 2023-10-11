<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\PromptListRepository;
use App\Repository\PromptRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    #[Route('/', name: 'app_home')]
    public function index(
        PostRepository       $postRepository,
        PromptRepository     $promptRepository,
        PromptListRepository $promptListRepository
    ): Response
    {
        $now = new \DateTimeImmutable();
        $currentYear = $now->format('Y');

        $currentPrompts = $promptRepository->findByYear($currentYear);
        $currentPosts = $postRepository->findAllByYear($currentYear);
        $currentList = $promptListRepository->findOneBy(['year' => $currentYear])->getYear();

        return $this->render('home/index.html.twig', [
            'currentPosts' => $currentPosts,
            'currentPrompts' => $currentPrompts,
            'currentList' => $currentList,
        ]);
    }

    #[Route('f/{promptName}', name: 'app_home_search_by_prompt')]
    public function searchByPrompt(
        PostRepository       $postRepository,
        string               $promptName,
        PromptRepository     $promptRepository,
        PromptListRepository $promptListRepository
    ): Response
    {
        $now = new \DateTimeImmutable();
        $currentYear = $now->format('Y');

        $currentPrompts = $promptRepository->findByYear($currentYear);
        $currentPosts = $postRepository->findAllByPrompt($promptName);
        $currentList = $promptListRepository->findOneBy(['year' => $currentYear])->getYear();

        return $this->render('home/index.html.twig', [
            'currentPosts' => $currentPosts,
            'currentPrompts' => $currentPrompts,
            'currentList' => $currentList,
        ]);
    }
}
