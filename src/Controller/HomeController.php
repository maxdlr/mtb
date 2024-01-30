<?php

namespace App\Controller;

use App\Repository\PromptListRepository;
use App\Repository\SocialLinkRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly SocialLinkRepository $socialLinkRepository,
        private readonly PromptListRepository $promptListRepository
    )
    {
    }

    #[Route('/', name: 'app_home')]
    public function default(): Response
    {
        $now = new DateTimeImmutable();
        $year = $now->format('Y');

        if (!$this->promptListRepository->findOneBy(['year' => $year]))
            $year = '2023';

        return $this->redirectToRoute('app_list', ['year' => $year]);
    }

    #[Route('/list/{year}', name: 'app_list')]
    public function index(
        int $year
    ): Response
    {
        $socialLinks = $this->socialLinkRepository->findAll();

        return $this->render('home/index.html.twig', [
            'year' => $year,
            'socialLinks' => $socialLinks,
        ]);
    }
}
