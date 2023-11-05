<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\PromptListRepository;
use App\Repository\PromptRepository;
use App\Repository\UserRepository;
use App\Service\DataManager;
use App\Service\PostManager;
use App\Service\SecurityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\isEmpty;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly PostRepository       $postRepository,
        private readonly PromptRepository     $promptRepository,
        private readonly PromptListRepository $promptListRepository,
        private readonly UserRepository       $userRepository,
        private readonly PostManager          $postManager
    )
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $now = new \DateTimeImmutable();
        $currentYear = $now->format('Y');
        $user = $this->userRepository->findOneBy(['username' => $this->getUser()?->getUserIdentifier()]);

        $latestUserPost = $this->postManager
            ->PostToArray($this->postManager->getLatestPost($user?->getPosts()));

        $posts = $this->postRepository->findAllBy('promptList.year', $currentYear, 'post.uploadedOn');

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'latestUserPost' => $latestUserPost,
            'currentYear' => $currentYear,
        ]);
    }

    #[Route('/f/{promptName}', name: 'app_home_search_by_prompt')]
    public function searchByPrompt(
        string      $promptName,
        DataManager $dataManager,
    ): Response
    {
        $now = new \DateTimeImmutable();
        $currentYear = $now->format('Y');

        $prompts = $this->promptRepository->findByYear($currentYear);
        $list = $this->promptListRepository->findOneBy(['year' => $currentYear])->getYear();

        if ($dataManager->isInFilteredArray($promptName, $prompts, 'nameEn')) {
            $posts = $this->postRepository->findAllBy('prompt.name_en', $promptName, 'post.uploadedOn');
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
