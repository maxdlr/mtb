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
        private readonly PostRepository $postRepository,
        private readonly UserRepository $userRepository,
    )
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $now = new \DateTimeImmutable();
        $currentYear = $now->format('Y');
        $user = $this->userRepository->findOneBy(['username' => $this->getUser()?->getUserIdentifier()]);

        if ($user)
            $latestUserPost = $this->postRepository->findLatestPostOfUser($user);

        $posts = $this->postRepository->findAllBy('promptList.year', $currentYear, 'post.uploadedOn');

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'latestUserPost' => $latestUserPost ?? null,
            'currentYear' => $currentYear,
            'user' => $user
        ]);
    }
}
