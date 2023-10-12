<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\PromptListRepository;
use App\Repository\PromptRepository;
use App\Service\DataManager;
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

        $prompts = $promptRepository->findByYear($currentYear);
        $posts = $postRepository->findAllByYear($currentYear);
        $list = $promptListRepository->findOneBy(['year' => $currentYear])->getYear();

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'prompts' => $prompts,
            'list' => $list,
        ]);
    }

    #[Route('/f/{promptName}', name: 'app_home_search_by_prompt')]
    public function searchByPrompt(
        PostRepository       $postRepository,
        string               $promptName,
        PromptRepository     $promptRepository,
        PromptListRepository $promptListRepository,
        DataManager          $dataManager,
    ): Response
    {
        $now = new \DateTimeImmutable();
        $currentYear = $now->format('Y');

        $prompts = $promptRepository->findByYear($currentYear);
        $list = $promptListRepository->findOneBy(['year' => $currentYear])->getYear();

        if ($dataManager->isInFilteredArray($promptName, $prompts, 'nameEn')) {
            $posts = $postRepository->findAllByPrompt($promptName);
        } else {
            $this->addFlash('danger', 'Thème inconnu');
            return $this->redirectToRoute('app_home');
        };

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'prompts' => $prompts,
            'list' => $list,
        ]);
    }
}
